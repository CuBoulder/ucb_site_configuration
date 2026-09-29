<?php

namespace Drupal\ucb_site_configuration\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Server-side proxy that compiles MJML into email HTML.
 * The newsletter MJML email preview web component POSTs the MJML produced by the newsletter MJML view mode templates to this endpoint.
 */
class NewsletterMjmlController extends ControllerBase {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a NewsletterMjmlController object
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger channel.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $config_factory, LoggerInterface $logger) {
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('config.factory'),
      $container->get('logger.factory')->get('ucb_site_configuration')
    );
  }

  /**
   * Compiles posted MJML into email HTML
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request, whose JSON body must contain an "mjml" property.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the compiled "html" (and any MJML "errors").
   */
  public function render(Request $request) {
    $payload = json_decode($request->getContent(), TRUE);
    $mjml = is_array($payload) ? ($payload['mjml'] ?? '') : '';

    if (!is_string($mjml) || trim($mjml) === '') {
      return new JsonResponse(['error' => 'No MJML content was provided.'], 400);
    }

    $config = $this->config('ucb_site_configuration.newsletter_mjml');
    $endpoint = $config->get('api_endpoint') ?: 'https://api.mjml.io/v1/render';
    $applicationId = $config->get('application_id');
    $secretKey = $config->get('secret_key');

    if (empty($applicationId) || empty($secretKey)) {
      $this->logger->error('MJML render requested but the MJML API credentials are not configured.');
      return new JsonResponse([
        'error' => 'The MJML render service is not configured. Add the MJML API credentials under CU Boulder site settings.',
      ], 500);
    }

    try {
      $response = $this->httpClient->post($endpoint, [
        'auth' => [$applicationId, $secretKey],
        'json' => ['mjml' => $mjml],
        'timeout' => 15,
      ]);

      $data = json_decode((string) $response->getBody(), TRUE);

      if (!is_array($data) || !isset($data['html'])) {
        $this->logger->error('MJML render endpoint returned an unexpected response.');
        return new JsonResponse(['error' => 'The MJML render service returned an unexpected response.'], 502);
      }

      return new JsonResponse([
        'html' => $this->lockOutlookWindowsChrome($data['html']),
        'errors' => $data['errors'] ?? [],
      ]);
    }
    catch (\Exception $e) {
      $this->logger->error('MJML render request failed: @message', ['@message' => $e->getMessage()]);
      if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
        $this->logger->error('MJML API response: @body', [
          '@body' => (string) $e->getResponse()->getBody(),
        ]);
      }
      return new JsonResponse(['error' => 'The MJML render service could not be reached.'], 502);
    }
  }

  /**
   * Word Engine Overrides
   *
   * Dark mode leaves the VML fill alone:
   * Inverts #ffffff text inside it, so labels render dark on the locked black bar.
   * mso-color-alt:auto stays white when the message background is light and the VML fill is #333333 or darker
   */
  protected function lockOutlookWindowsChrome(string $html): string {
    if ($html === '') {
      return $html;
    }

    $chromeBlack = '#1A1A1A';
    $vmlOpen = '<!--[if gte mso 9]><v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:100%;mso-width-percent:1000;"><v:fill type="tile" color="' . $chromeBlack . '" /><v:textbox inset="0,0,0,0" style="mso-fit-shape-to-text:true"><![endif]-->';
    $vmlClose = '<!--[if gte mso 9]></v:textbox></v:rect><![endif]-->';

    foreach ([
      ['<!-- ucb-header-lock-start -->', '<!-- ucb-header-lock-end -->'],
      ['<!-- ucb-footer-lock-start -->', '<!-- ucb-footer-lock-end -->'],
    ] as [$startMarker, $endMarker]) {
      if (strpos($html, $startMarker) !== FALSE && strpos($html, $endMarker) !== FALSE) {
        $html = str_replace($startMarker, $startMarker . $vmlOpen, $html);
        $html = str_replace($endMarker, $vmlClose . $endMarker, $html);
      }
    }

    $html = $this->forceOutlookHeaderLabelColor($html);

    $headLock = '<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->'
      . '<!--[if mso]><style type="text/css">'
      . '.header,.header table,.header td,.header div,'
      . '.footer,.footer table,.footer td,.footer div{'
      . 'background:transparent !important;background-color:transparent !important;}'
      . '.header-chrome-label,.header-chrome-label div,.header-chrome-label p,'
      . '.header-chrome-label span,.header-chrome-label font{mso-color-alt:auto !important;}'
      . '</style><![endif]-->';

    if (stripos($html, 'ucb-outlook-chrome-lock') === FALSE) {
      $headLock = '<!-- ucb-outlook-chrome-lock -->' . $headLock;
      if (stripos($html, '</head>') !== FALSE) {
        $html = preg_replace('/<\/head>/i', $headLock . '</head>', $html, 1);
      }
      else {
        $html = $headLock . $html;
      }
    }

    return $html;
  }

  /**
   * Applies mso-color-alt:auto onto the white header labels
   */
  protected function forceOutlookHeaderLabelColor(string $html): string {
    $startMarker = '<!-- ucb-header-lock-start -->';
    $endMarker = '<!-- ucb-header-lock-end -->';
    $startPos = strpos($html, $startMarker);
    $endPos = strpos($html, $endMarker);
    if ($startPos === FALSE || $endPos === FALSE || $endPos <= $startPos) {
      return $html;
    }

    $endPos += strlen($endMarker);
    $header = substr($html, $startPos, $endPos - $startPos);
    $header = preg_replace_callback(
      '/<([a-zA-Z][\w:-]*)([^>]*)>/',
      function (array $matches): string {
        $attrs = $matches[2];
        if (stripos($attrs, 'mso-color-alt') !== FALSE) {
          return $matches[0];
        }

        $isLabel = (bool) preg_match('/\bclass=(["\'])[^"\']*\bheader-chrome-label\b[^"\']*\1/i', $attrs);
        $isWhite = FALSE;
        if (preg_match('/\bstyle=(["\'])(.*?)\1/is', $attrs, $style)) {
          $isWhite = $this->styleDeclaresWhiteColor($style[2]);
        }
        if (!$isLabel && !$isWhite) {
          return $matches[0];
        }

        return '<' . $matches[1] . $this->appendMsoColorAlt($attrs) . '>';
      },
      $header
    );

    return substr($html, 0, $startPos) . $header . substr($html, $endPos);
  }

  /**
   * Whether a CSS declaration block sets the text color to white
   */
  protected function styleDeclaresWhiteColor(string $css): bool {
    foreach (explode(';', $css) as $declaration) {
      $declaration = trim($declaration);
      if ($declaration === '' || !str_contains($declaration, ':')) {
        continue;
      }
      [$property, $value] = array_map('trim', explode(':', $declaration, 2));
      if (strcasecmp($property, 'color') !== 0) {
        continue;
      }
      $value = strtolower((string) preg_replace('/\s*!important\s*$/i', '', $value));
      if (in_array($value, ['#ffffff', '#fff', 'white'], TRUE)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Appends mso-color-alt:auto to an element's style
   */
  protected function appendMsoColorAlt(string $attrs): string {
    if (stripos($attrs, 'mso-color-alt') !== FALSE) {
      return $attrs;
    }
    if (preg_match('/\bstyle=(["\'])/i', $attrs, $match, PREG_OFFSET_CAPTURE)) {
      $quote = $match[1][0];
      $valueStart = $match[0][1] + strlen($match[0][0]);
      $valueEnd = strpos($attrs, $quote, $valueStart);
      if ($valueEnd === FALSE) {
        return $attrs . ' style="mso-color-alt:auto;"';
      }
      $css = rtrim(substr($attrs, $valueStart, $valueEnd - $valueStart));
      if ($css !== '' && !str_ends_with($css, ';')) {
        $css .= ';';
      }
      $css .= 'mso-color-alt:auto;';
      return substr($attrs, 0, $valueStart) . $css . substr($attrs, $valueEnd);
    }
    return rtrim($attrs) . ' style="mso-color-alt:auto;"';
  }

}

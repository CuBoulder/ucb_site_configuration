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
   * Constructs a NewsletterMjmlController object.
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
   * Compiles posted MJML into email HTML.
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
        'html' => $data['html'],
        'errors' => $data['errors'] ?? [],
      ]);
    }
    catch (\Exception $e) {
      $this->logger->error('MJML render request failed: @message', ['@message' => $e->getMessage()]);
      return new JsonResponse(['error' => 'The MJML render service could not be reached.'], 502);
    }
  }

}

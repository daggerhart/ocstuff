<?php

namespace Drupal\opencollective\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\oc_graphql_client\Plugin\OcQuery\CollectiveEvents;
use Drupal\oc_graphql_client\Service\GraphQLClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Api Events' formatter.
 *
 * @FieldFormatter(
 *   id = "opencollective_api_events",
 *   label = @Translation("Api Events"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class ApiEvents extends FormatterBase {

  /**
   * GraphQl Client.
   *
   * @var GraphQLClient
   */
  private GraphQLClient $client;

  /***
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $configuration
   *   The plugin configuration.
   * @param GraphQLClient $client
   *   GraphQl Client.
   */
  public function __construct($plugin_id, $plugin_definition, array $configuration, GraphQLClient $client) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings']
    );

    $this->client = $client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration,
      $container->get('oc_graphql_client.graphql_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'event_properties' => 'id slug name',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['event_properties'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Properties'),
      '#description' => $this->t('List the properties desired, separated by spaces.'),
      '#default_value' => $this->getSetting('event_properties'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = $this->t('Event Properties: @event_properties', ['@event_properties' => $this->getSetting('event_properties')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $query = $this->client->queryPluginManager()->createInstance(CollectiveEvents::PLUGIN_ID);

      // $item->value
      $element[$delta] = [
        '#theme' => 'opencollective_api_events',
        '#collective_slug' => $item->value,
        '#events' => $this->client->performQuery($query, [
          'collective_slug' => $item->value,
          'event_properties' => $this->getSetting('event_properties'),
        ]),
      ];
    }

    return $element;
  }

}

<?php

namespace Drupal\ocstuff\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\oc_graphql_client\Service\GraphQLClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Api Events' formatter.
 *
 * @FieldFormatter(
 *   id = "ocstuff_api_events",
 *   label = @Translation("Api Events"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class ApiEvents extends FormatterBase {


  /**
   * @var GraphQLClient
   */
  private GraphQLClient $client;

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
      //'foo' => 'bar',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [];
    $elements['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->getSetting('foo'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return [];
    $summary[] = $this->t('Foo: @foo', ['@foo' => $this->getSetting('foo')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // $item->value
      $element[$delta] = [
        '#theme' => 'ocstuff_api_events',
        '#collective_slug' => $item->value,
        '#events' => $this->client->getCollectiveEvents($item->value),
      ];
    }

    return $element;
  }

}

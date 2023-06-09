<?php

namespace Drupal\opencollective\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Plugin implementation of the 'EmbedButtons' formatter.
 *
 * @FieldFormatter(
 *   id = "opencollective_embed_button",
 *   label = @Translation("Embed OpenCollective Button"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class EmbedButton extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'color' => 'blue',
      'verb' => 'contribute',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements['color'] = [
      '#type' => 'select',
      '#title' => $this->t('Color'),
      '#description' => $this->t(''),
      '#default_value' => $this->getSetting('color'),
      '#options' => [
        'blue' => $this->t('Blue'),
        'white' => $this->t('White'),
      ],
    ];
    $elements['verb'] = [
      '#type' => 'select',
      '#title' => $this->t('Verb'),
      '#description' => $this->t(''),
      '#default_value' => $this->getSetting('verb'),
      '#options' => [
        'contribute' => $this->t('Contribute'),
        'donate' => $this->t('Donate'),
      ],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = $this->t('Color: @color', ['@color' => $this->getSetting('color')]);
    $summary[] = $this->t('Verb: @verb', ['@verb' => $this->getSetting('verb')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#theme' => 'opencollective_embed_button',
        '#collective_slug' => $item->value,
        '#color' => $this->getSetting('color'),
        '#verb' => $this->getSetting('verb'),
      ];
    }

    return $element;
  }

}

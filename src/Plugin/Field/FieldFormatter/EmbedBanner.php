<?php

namespace Drupal\opencollective\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Plugin implementation of the 'EmbedBanner' formatter.
 *
 * @FieldFormatter(
 *   id = "opencollective_embed_banner",
 *   label = @Translation("Embed OpenCollective Banner"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class EmbedBanner extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'banner_type' => 'supporters',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements['banner_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Banner_type'),
      '#description' => $this->t(''),
      '#default_value' => $this->getSetting('banner_type'),
      '#options' => [
        'supporters' => $this->t('Supporters'),
        'events' => $this->t('Events'),
      ],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = $this->t('Banner Type: @banner_type', ['@banner_type' => $this->getSetting('banner_type')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Default to supporters.
      $embed = [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#attributes' => [
          'src' => "//opencollective.com/{$item->value}/banner.js",
          'class' => [
            'opencollective-embed-script',
          ],
        ],
      ];

      // Or events.
      if ($this->getSetting('banner_type') === 'events') {
        $embed['#attributes']['src'] = "//opencollective.com/{$item->value}/events.js";
      }

      $element[$delta] = $embed;
    }

    return $element;
  }

}

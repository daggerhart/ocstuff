<?php

namespace Drupal\opencollective\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Plugin implementation of the 'EmbedContributionFlow' formatter.
 *
 * @FieldFormatter(
 *   id = "opencollective_embed_contribution_flow",
 *   label = @Translation("Embed OpenCollective Contribution Flow"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class EmbedContributionFlow extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'tier' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements['tier'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tier'),
      '#description' => $this->t('The slug for the tier of contribution to be embedded. By default, the "donate" tier is used.'),
      '#default_value' => $this->getSetting('tier'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = $this->t('Tier: @tier', ['@tier' => $this->getSetting('tier')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Default to donate.
      $embed = [
        '#type' => 'html_tag',
        '#tag' => 'iframe',
        '#attributes' => [
          'src' => "https://opencollective.com/embed/{$item->value}/donate",
          'class' => [
            'opencollective-embed-iframe',
          ],
        ],
        '#attached' => [
          'library' => [
            'opencollective/embed-iframe',
          ],
        ],
      ];

      // Tier specific flow.
      if ($this->getSetting('tier')) {
        $embed['#attributes']['src'] = "https://opencollective.com/embed/{$item->value}/contribute/{$this->getSetting('tier')}";
      }

      $element[$delta] = $embed;
    }

    return $element;
  }

}

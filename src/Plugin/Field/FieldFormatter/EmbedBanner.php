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
 *   description = @Translation("Banner shows a list of collective supporters."),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class EmbedBanner extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#theme' => 'opencollective_embed_banner',
        '#collective_slug' => $item->value,
      ];
    }

    return $element;
  }

}

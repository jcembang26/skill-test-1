<?php

namespace Drupal\alias_hierarchy\Plugin\Field\FieldWidget;

use Drupal\Core\Utility\Token;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\pathauto\AliasCleaner;

/**
 * Plugin implementation of the 'alias_hierarchy' widget.
 *
 * @FieldWidget(
 *   id = "alias_hierarchy",
 *   label = @Translation("Alias Hierarchy"),
 *   field_types = {
 *     "string"
 *   }
 * )
 *
 * @codeCoverageIgnore Covered by functional tests.
 */
final class AliasHierarchyWidget extends StringTextfieldWidget {

  /**
   * Token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * Pathauto alias cleaner.
   *
   * @var \Drupal\pathauto\AliasCleaner
   */
  protected $aliasCleaner;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, Token $token, AliasCleaner $alias_cleaner) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->token = $token;
    $this->aliasCleaner = $alias_cleaner;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('token'),
      $container->get('pathauto.alias_cleaner')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Call parent class.
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Add JS that populates fieldset summary for a better user experience.
    $element['value']['#attached']['library'] = ['alias_hierarchy/widget'];

    /** @var \Drupal\node\Entity\Node */
    $entity = $items->getEntity();

    // Add our prefix field.
    $element['prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path prefix'),
      '#description' => $this->t("The prefix cannot be changed and is derived from the node's parents."),
      '#disabled' => TRUE,
      '#weight' => -1,
      '#default_value' => (empty($entity) || $entity->isNew()) ? '' : $this->token->replace('[node:alias-hierarchy-parents:join-path]', ['node' => $entity]),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    // Clean value provided by the user so the field value matches the actual
    // path. For instance if the user enters a custom path with special
    // characters such as ! or \ then pathauto will automatically transliterate
    // those. Hence we want the field value to store the clean (transliterated)
    // value not to confuse the end user.
    foreach ($values as $delta => $value) {
      $values[$delta] = $this->aliasCleaner->cleanString($value['value']);
    }
    return $values;
  }

}

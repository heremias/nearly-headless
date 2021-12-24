<?php

namespace Drupal\standard_core\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Slug constraint.
 */
class SlugValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    foreach ($items as $item) {
      // First check if the value is an integer.
      if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $item->value)) {
        // The value is not an integer, so a violation, aka error, is applied.
        // The type of violation applied comes from the constraint description
        // in step 1.
        $this->context->addViolation($constraint->invalidCharacters, ['%value' => $item->value]);
      }
    }
  }

}

<?php

namespace Drupal\standard_core\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a unique integer.
 *
 * @Constraint(
 *   id = "Slug",
 *   label = @Translation("Slug", context = "Validation"),
 *   type = "string"
 * )
 */
class Slug extends Constraint {

  // The message that will be shown if the value is not an integer.
  public $invalidCharacters = '%value must consist of lower case letters, numbers, and hyphens and begin with a letter or number.';

}

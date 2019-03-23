<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesChoiceType extends AbstractType
{
    /**
     * @var array
     */
    protected $roles;

    /**
     * RolesChoiceType constructor.
     */
    public function __construct(array $roles)
    {
        $this->roles = array_keys($roles);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[$role] = $role;
        }

        $resolver->setDefaults([
            'choices' => $roles,
            'multiple' => true,
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

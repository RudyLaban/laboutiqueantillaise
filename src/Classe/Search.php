<?php

namespace App\Classe;

use App\Entity\Category;

/**
 * Objet sur lequel s'appuit le formulaire de recherche SearchType
 */
class Search
{
    /**
     * @var string
     */
    public $string = '';

    /**
     * @var Category[]
     */
    public $categories = [];

}
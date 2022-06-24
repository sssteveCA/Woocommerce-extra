<?php
namespace WoocommerceExtra\Interfaces;

//Error constants of CatBreadcrumb class

interface CatBreadcrumbErrors{
    //Exceptions
    const VALUENOTSET_EXC = "Uno o più valori richiesti non sono stati definiti";
    const CATEGORIES_PARAM_NOTARRAY = "Il parametro 'categories' non è un array";
    const INCORRECTPATTERN_EXC = "La stringa passata che contiene le categorie del prodotto, ha un formato sconosciuto";
    const URL_CAT_ARRAY_LENGTHMISMATCH = "La lunghezza degli array dei nomi delle cateogie e degli indirizzi non è identica";

}

?>
<?php

    function tokenisation($text,$replace){
        //Traitement de la casse
        $texte = strtolower($text);

        //Suppression des chiffres et des symboles
        $texte = preg_replace('/[0-9.,;:!+*-?()\/\']/', ' ', $texte);
        $texte = str_replace(array("«", "»", "’’"), "", $texte);
        $texte = str_replace(array("é", "è", "ê"), "e", $texte);
        $texte = str_replace($replace, " ", $texte);
        
        return $texte;
    }
?>
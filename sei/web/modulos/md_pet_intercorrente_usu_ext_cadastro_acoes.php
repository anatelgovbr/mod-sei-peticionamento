<?php
    /**
     * ANATEL
     *
     * 25/11/2016 - criado por marcelo.bezerra@cast.com.br - CAST
     * Controle de aчѕes principais do cadastro de peticionamento intercorrente
     *
     */

    switch ($_GET['acao']) {

        case 'md_pet_intercorrente_usu_ext_cadastrar':
            $strTitulo = 'Peticionamento Intercorrente';
            break;

        default:
            throw new InfraException("Aчуo '" . $_GET['acao'] . "' nуo reconhecida.");
    }
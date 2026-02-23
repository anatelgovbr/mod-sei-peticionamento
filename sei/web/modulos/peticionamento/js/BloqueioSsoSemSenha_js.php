<? if(MdPetUsuarioExternoRN::usuarioSsoSemSenha()): ?>
    <script type="text/javascript">
        infraAbrirJanelaModal('<?=  SessaoSEIExterna::getInstance()->assinarLink("controlador_externo.php?acao=md_pet_usu_ext_bloqueio_senha_sso") ?>', 700, 250, true, function(){
            window.location.href = '<?= SessaoSEIExterna::getInstance()->assinarLink("controlador_externo.php?acao=usuario_externo_inicializar_senha") ?>';
        });
    </script>
<? endif; ?>

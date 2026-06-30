$(document).ready(function() {
    // Ação ao clicar no botão de edição
    $("#editButton").on("click", function() {
        $("form input[type=text], form textarea, form select, form input[type=checkbox]").removeAttr("disabled"); // Habilitar todos os elementos do formulário
        $("#answer").css("background-color", "#cce5ff"); // Mudar a cor de fundo para azul clara
        $("#saveButton").show(); // Mostrar o botão Save
        $(this).hide(); // Esconder o botão de edição
    });

    // Ação ao clicar no botão Save
    $("#saveButton").on("click", function() {
        // Submeter o formulário para salvar a resposta
        $("#editAnswerForm").submit();
    });
});
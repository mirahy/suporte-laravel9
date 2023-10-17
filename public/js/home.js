var guias = ['solicitacoes', 'universidade', 'administracao', 'gestaoUsuarios', 'cursos'];
// ocultar guia aberta ao abrir outra
$('button').click(function() {
    $('div').removeClass('in');
    guias.forEach(element => {
        //Alterar seta para baixo de todas as guias ao clicar
        $("#img"+element).removeClass('bi-caret-up-fill');
        $("#img"+element).addClass('bi-caret-down-fill');
    });
});

//Alterar seta para cima somente da guia em aberto
guias.forEach(element => {
    $("#"+element).on("show.bs.collapse", function(){
        $("#img"+element).removeClass('bi-caret-down-fill');
        $("#img"+element).addClass('bi-caret-up-fill');
    });
});


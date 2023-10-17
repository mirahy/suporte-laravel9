import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from 'src/app/abstract-component';
import { AbstractPLDAComponentInterface } from 'src/app/abstract-plda-component-interface';
import { Arvore } from 'src/app/arvore';
import { Estudante } from 'src/app/pl-disciplinas-academicos/estudante';
import { PlDisciplinasAcademicos } from 'src/app/pl-disciplinas-academicos/pl-disciplinas-academicos';
import { ServidoresMoodleService } from 'src/app/servidores-moodle.service';
import { UnidadeOrganizacionalService } from 'src/app/unidade-organizacional.service';
declare var jQuery: any;

@Component({
	selector: 'app-formulario-alteracao-usuario',
	templateUrl: './formulario-alteracao-usuario.component.html',
	styleUrls: ['./formulario-alteracao-usuario.component.less']
})
export class FormularioAlteracaoUsuarioComponent extends AbstractComponent implements AbstractPLDAComponentInterface,OnInit {

	constructor(private unidadeOrganizacionalService: UnidadeOrganizacionalService, private servidoresMoodleService: ServidoresMoodleService) {
		super();
	}

    readonly SELECAO_MEMBRO_DE = false;
	
	estudantes:Array<Estudante> = [];
	estudanteTemp = Estudante.generateEstudante();

    linksMoodles = [];

    usarSenha = false;
    senhaPadrao = "";

    plDisciplinasAcademicosTemp: PlDisciplinasAcademicos = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
    periodoLetivoSelecionadoId = "";
    cursoSelecionadoId = "";
    faculdadeSelecionadaId = "";
    disciplinaSelecionadaNome = "";

    //apenas implementação da interface, não utilizados por aqui
	plDisciplinasAcademicosTempList = [];
	modoLista = false;
    disciplinaSelecionadaNomes:Array<string> = [];

    get eu() {
        return this;
    }

	novoEstudante () {
		this.estudanteTemp = Estudante.generateEstudante();
	}
	adicionarEstudante() {
        if (this.estudanteTemp.username.length) {
            this.estudantes.push(new Estudante (this.estudanteTemp.username, this.estudanteTemp.email, this.estudanteTemp.fullname, this.estudanteTemp.is_professor, this.estudanteTemp.email_alternativo));
            jQuery('#dialogCreateEstudante').modal('hide');
            this.estudanteTemp = Estudante.generateEstudante();
        }
        else 
            alert ("Usuário Inválido!");
    }
    removerEstudante(estudante:Estudante) {
        if (!confirm ("Deseja remover este estudante")) 
            return;
        var i = 0;
        for (; i < this.estudantes.length; i++) {
            if (estudante.equals (this.estudantes[i]))
                break;
        }
        if (i < this.estudantes.length) {
            this.estudantes.splice (i, 1);
        }
    }
	limparEstudantes() {
        if (confirm ("Deseja remover todos os estudantes"))
            this.estudantes = [];
    }
	lerAlunosCSV (event) {
        var fileExtension = /.*\.csv/;
        var fileTobeRead = event.target.files[0];
        if (fileTobeRead.name.toLowerCase().match(fileExtension)) {
            var fileReader = new FileReader();
            var _this = this;
            fileReader.onload = function (e) {
                _this.estudantes =  Estudante.processaCSVcomSenha(fileReader.result);
            }
            fileReader.readAsText(fileTobeRead);
        }
        else {
            alert("Por favor selecione arquivo csv");
        }

    }

    alterarSenhasUsuariosAD() {
        if (!this.estudantes.length) {
            alert("Não há usuários para redefinir senha!")
            return;
        }
        if (!confirm("Deseja alterar a senha destes usuários?")) 
            return;
        jQuery('#dialogExportResult').modal('show');
        jQuery('#saidaExport').html("<i>Aguarde...<i>");
        this.editavel = false;
        this.aviso = "";
        this.unidadeOrganizacionalService.alteraSenhaUsuarios(Estudante.converteEstudantesParaJSONcomSenha( this.estudantes ), this.usarSenha ? this.senhaPadrao : "")
            .then(response => {
                jQuery('#saidaExport').html(response);
                this.editavel = true;
            })
            .catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.editavel = true;
                jQuery('#saidaExport').html('<span style="color: red;">'+this.aviso+"</span>");
            });
        
    }

    
    carregarEstudantesSigecad() {
        if (this.plDisciplinasAcademicosTemp && confirm("Confirmar carregamento de lista de Estudantes do SIGECAD?")) {
            this.editavel = false;
            this.servidoresMoodleService.getAcademicosDisciplinasSigecad(this.plDisciplinasAcademicosTemp.disciplina_key,this.periodoLetivoSelecionadoId,this.plDisciplinasAcademicosTemp.turma_id, this.plDisciplinasAcademicosTemp.turma_nome)
                .then(est => {
                    this.plDisciplinasAcademicosTemp.estudantes = est;
                    this.estudantes = est;
                    this.editavel = true;
                    jQuery('#dialogBuscaImport').modal('hide');
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso)
                    this.editavel = true;
                });
        }
    }

	ngOnInit() {
		this.status = this.COMPLETE;
		this.editavel = true;		
	}

}

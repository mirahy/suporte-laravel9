import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from 'src/app/abstract-component';
import { AbstractPLDAComponentInterface } from 'src/app/abstract-plda-component-interface';
import { PlDisciplinasAcademicosService } from 'src/app/pl-disciplinas-academicos.service';
import { Estudante } from 'src/app/pl-disciplinas-academicos/estudante';
import { PlDisciplinasAcademicos } from 'src/app/pl-disciplinas-academicos/pl-disciplinas-academicos';
import { ServidoresMoodleService } from 'src/app/servidores-moodle.service';
declare var jQuery: any;

@Component({
	selector: 'app-formulario-insercao-usuarios-moodle',
	templateUrl: './formulario-insercao-usuarios-moodle.component.html',
	styleUrls: ['./formulario-insercao-usuarios-moodle.component.less']
})
export class FormularioInsercaoUsuariosMoodleComponent extends AbstractComponent implements AbstractPLDAComponentInterface, OnInit {

	constructor(private servidoresMoodleService: ServidoresMoodleService,private plDisciplinasAcademicosService:PlDisciplinasAcademicosService) { 
		super();
	}

	estudantes:Array<Estudante> = [];
	estudanteTemp = Estudante.generateEstudante();

    linkServidorMoodleExportar:string = "";
    linksMoodles = [];

    usarSala = false;
    usarSenha = false;
    salaExportar = "";
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
        if (this.estudanteTemp.isValid()) {
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

    exportarEstudantes() {
        if (!this.estudantes.length) {
            alert("Não há usuários para exportar!")
            return;
        }
        if (!this.linkServidorMoodleExportar) {
            alert("Selecione um servidor moodle primeiro!")
            return;
        }
        if (!confirm("Deseja inserir estes usuários no Moodle selecionado?")) 
            return;
        jQuery('#dialogExportResult').modal('show');
        jQuery('#saidaExport').html("<i>Aguarde...<i>");
        this.editavel = false;
        //this.blockAutoRestore = true;
        this.servidoresMoodleService.exportarEstudantes(Estudante.converteEstudantesParaJSONcomSenha( this.estudantes ), this.linkServidorMoodleExportar, this.usarSala ? this.salaExportar : "", this.usarSenha ? this.senhaPadrao : "")
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
		this.editavel = true;
        this.servidoresMoodleService.getLinksServidoresMoodle()
            .then(response => {
                this.linksMoodles = response;
            })
            .catch(r => {
                alert(this.erroHttp(r));
            });
	}
}

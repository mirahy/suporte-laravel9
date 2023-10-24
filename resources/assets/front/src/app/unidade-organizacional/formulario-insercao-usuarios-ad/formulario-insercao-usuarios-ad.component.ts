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
	selector: 'app-formulario-insercao-usuarios-ad',
	templateUrl: './formulario-insercao-usuarios-ad.component.html',
	styleUrls: ['./formulario-insercao-usuarios-ad.component.less']
})
export class FormularioInsercaoUsuariosAdComponent extends AbstractComponent implements AbstractPLDAComponentInterface,OnInit {

	constructor(private unidadeOrganizacionalService: UnidadeOrganizacionalService, private servidoresMoodleService: ServidoresMoodleService) {
		super();
	}

    readonly SELECAO_MEMBRO_DE = false;
	
	estudantes:Array<Estudante> = [];
	estudanteTemp = Estudante.generateEstudante();

    linksMoodles = [];

    ousPastaCreate:Arvore[] = [null];
    ousPastaCreateSelected:Array<number|string> = [""];
	ous = [];

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
	get unidadesOrganizacionais () {
		return this.unidadeOrganizacionalService.unidadesOrganizacionais;
	}
    get unidadesOrganizacionaisNomes () {
		return this.unidadeOrganizacionalService.unidadesOrganizacionais.map(function (e) {return e.nome;}).join("\n");
	}
    get ouDirRoot() {
        return this.unidadeOrganizacionalService.ouDirRoot;
    }
    get arvoreOU():Arvore {
        return this.unidadeOrganizacionalService.arvoreOU;
    }
    get ou_str() {
        return this.unidadeOrganizacionalService.ou_str;
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

    substituirEmailsPorPadrao () {
        if (!confirm ("Deseja substituir os emails destes estudantes pelo padrão?"))
            return;
        this.editavel = true;
        this.unidadeOrganizacionalService.substituiEmailsPorPadrao(Estudante.converteEstudantesParaJSONcomSenha( this.estudantes ))
            .then(response => {
                this.estudantes = response;
                this.editavel = true;
            })
            .catch(response => {
                alert (this.erroHttp(response));
                this.editavel = true;
            });
    }

    get geraDirCadastro () {
        var _this = this;
        var ousArrConcat:any = this.ousPastaCreate.map(function (o:any) {
            return o ? _this.ou_str+o.dado : ''
        });
        ousArrConcat.pop();
        ousArrConcat = ousArrConcat.reverse().toString();
        return ousArrConcat + (ousArrConcat ? "," : "") + this.ouDirRoot;
    }

    selecionaPastaOU (paiId, index) {
        var nodoIndex = this.ousPastaCreateSelected[index];
        var nodo = null;
        if (nodoIndex)
            nodo = this.arvoreOU.find(<number> nodoIndex);
        if (nodo) {
            if (paiId) {
                for (var i = 0; i < this.ousPastaCreate.length; i++) {
                    if (this.ousPastaCreate[i] && this.ousPastaCreate[i].id == paiId) {
                        this.ousPastaCreate = this.ousPastaCreate.slice(0,i+1);
                        this.ousPastaCreateSelected = this.ousPastaCreateSelected.slice(0,i+1);
                        this.ousPastaCreate.push(nodo);
                        this.ousPastaCreateSelected.push(nodo.id);
                        this.ousPastaCreate.push(null);
                        this.ousPastaCreateSelected.push('');
                        break;
                    }
                }
            }
            else {
                this.ousPastaCreate = [nodo, null];
                this.ousPastaCreateSelected = [nodo.id, ''];
            }
        }
        else {
            if (paiId) {
                for (var i = 0; i < this.ousPastaCreate.length; i++) {
                    if (this.ousPastaCreate[i] && this.ousPastaCreate[i].id == paiId) {
                        this.ousPastaCreate = this.ousPastaCreate.slice(0,i+1);
                        this.ousPastaCreateSelected = this.ousPastaCreateSelected.slice(0,i+1);
                        this.ousPastaCreate.push(null);
                        this.ousPastaCreateSelected.push('');
                        break;
                    }
                }
            }
            else {
                this.ousPastaCreate = [null];
                this.ousPastaCreateSelected = [''];
            }
        }
    }

    criarUsuariosAD() {
        if (!this.estudantes.length) {
            alert("Não há usuários para exportar!")
            return;
        }
        if (!confirm("Deseja inserir estes usuários no AD?")) 
            return;
        jQuery('#dialogExportResult').modal('show');
        jQuery('#saidaExport').html("<i>Aguarde...<i>");
        this.editavel = false;
        this.aviso = "";
        this.unidadeOrganizacionalService.criarContasAD(this.geraDirCadastro, this.ous, Estudante.converteEstudantesParaJSONcomSenha( this.estudantes ))
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
		this.editavel = false;
		this.unidadeOrganizacionalService.getOuDirRoot() 
        .then(response => {
            this.unidadeOrganizacionalService.listar() 
                .then(response => {
                    this.unidadeOrganizacionalService.getOuFilhas()
                        .then(response => {
                            this.status = this.COMPLETE;
                            this.ous = this.unidadesOrganizacionais.map(function (u) {return u.id} );
                            this.editavel = true;
                        })
                        .catch(response => {
                            this.erroAviso = true;
                            this.aviso = this.erroHttp(response);
                            this.status = this.ERROR;
                            alert(this.aviso);
                        })
                })
                .catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    this.status = this.ERROR;
                    alert(this.aviso);
                })
        })
        .catch(response => {
            this.erroAviso = true;
            this.aviso = this.erroHttp(response);
            this.status = this.ERROR;
            alert(this.aviso);
        })
		
	}

}

import { Component, OnInit } from '@angular/core';
import { AbstractComponentChild } from '../abstract-component-child';
import { AbstractPLDAComponentInterface } from '../abstract-plda-component-interface';
import { CursosService } from '../cursos.service';
import { Curso } from '../cursos/curso';
import { FaculdadeService } from '../faculdade.service';
import { Faculdade } from '../faculdades/faculdade';
import { LoteSalasSimplificadoService } from '../lote-salas-simplificado.service';
import { MacroService } from '../macro.service';
import { PeriodoLetivosService } from '../periodo-letivos.service';
import { PeriodoLetivo } from '../periodo-letivos/periodo-letivo';
import { PlDisciplinasAcademicosService } from '../pl-disciplinas-academicos.service';
import { PlDisciplinasAcademicos } from '../pl-disciplinas-academicos/pl-disciplinas-academicos';
import { SalaSimplificadaService } from '../sala-simplificada.service';
import { Usuario } from '../usuarios/usuario';
import { SalaSimplificada } from './sala-simplificada';
declare var jQuery: any;

@Component({
	selector: 'app-sala-simplificada',
	templateUrl: './sala-simplificada.component.html',
	styleUrls: ['./sala-simplificada.component.less']
})
export class SalaSimplificadaComponent extends AbstractComponentChild implements AbstractPLDAComponentInterface, OnInit {

	constructor(private salaSimplificadaService: SalaSimplificadaService, private loteSalasSimplificadoService: LoteSalasSimplificadoService, private periodoLetivosService: PeriodoLetivosService,
		private cursosService: CursosService, private plDisciplinasAcademicosService:PlDisciplinasAcademicosService, private macroService:MacroService) {
		super();
	}

	emCriacao = false;
	courseImportId = "";

	COLUNAS = {
        id: true,
        periodo_letivo: false,
        nome_professor: false,
        faculdade: false,
        curso_key: false,
        curso: false,
        nome_sala: true,
        disciplina_key: false,
        turma_nome: false,
        turma_id: false,
        carga_horaria_total_disciplina: false,
        avaliacao: false,
        sala_moodle_id: true,
    }

	get salasSimplificadas () : Array<SalaSimplificada> {
		return this.salaSimplificadaService.salasSimplificadas;
	}
	set salasSimplificadas (salasSimplificadas) {
		this.salaSimplificadaService.salasSimplificadas = salasSimplificadas;
	}
    get plDisciplinasAcademicos () :Array<PlDisciplinasAcademicos>{
        return this.plDisciplinasAcademicosService.plDisciplinasAcademicos;
    }
	get salaSimplificadaTemp () {
		return this.salaSimplificadaService.salaSimplificadaSelecionada;
	}
	set salaSimplificadaTemp (salaSimplificadaTemp) {
		this.salaSimplificadaService.salaSimplificadaSelecionada = salaSimplificadaTemp;
	}
	get loteSelecionado () {
		return this.loteSalasSimplificadoService.loteSalasSimplificadoSelecionada;
	}
	set loteSelecionado (lote) {
		this.loteSalasSimplificadoService.loteSalasSimplificadoSelecionada = lote;
	}
	get macros () {
		return this.macroService.macros;
	}
	get eu() {
        return this;
    }

	plDisciplinasAcademicosSelecionado:PlDisciplinasAcademicos = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
	plDisciplinasAcademicosSelecionadoList:Array<PlDisciplinasAcademicos> = [];
	salaMoodleId:any = "";
	linkMoodle = "";

	// AbstractPLDAComponent
	plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
	plDisciplinasAcademicosTempList = [];
	modoLista = false;
	periodoLetivoSelecionadoId = "";
    cursoSelecionadoId = "";
    faculdadeSelecionadaId = "";
    disciplinaSelecionadaNome = "";
    disciplinaSelecionadaNomes:Array<string> = [];

	macroTempId = "";
    blockAutoRestore = false;

	selecionaPLDA () {
		this.plDisciplinasAcademicosSelecionado = this.plDisciplinasAcademicosTemp.clone();
		this.plDisciplinasAcademicosSelecionadoList = [];
		for (var i in this.plDisciplinasAcademicosTempList)
			this.plDisciplinasAcademicosSelecionadoList.push(this.plDisciplinasAcademicosTempList[i].clone())
	}

	resetPLDA () {
		this.plDisciplinasAcademicosSelecionado = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
		this.plDisciplinasAcademicosSelecionadoList = [];
	}

	criarAlterarSalaSimplificada(ev) {
        ev.preventDefault();
        var loteSalasForm = jQuery('#salaSimplificadaForm')[0];
		jQuery('#plda-disciplina').prop('readonly', false);
		if (loteSalasForm.reportValidity()) {
			jQuery('#plda-disciplina').prop('readonly', true);
            if (this.salaSimplificadaTemp.id)
                this.alterar();
            else
                this.criar();
		}
		else
			jQuery('#plda-disciplina').prop('readonly', true);
    }

	criar() {
		this.editavel = false;
		this.salaSimplificadaService.create(SalaSimplificada.generatePostSalaSimplificada(
			0,
			this.plDisciplinasAcademicosSelecionado,
			this.salaMoodleId,
			this.periodoLetivosService.periodoLetivosIdIndex.get(this.periodoLetivoSelecionadoId),
			this.loteSalasSimplificadoService.loteSalasSimplificadoSelecionada.id,
			null,
			this.salaMoodleId ? this.linkMoodle : '')
		).then (response => {
			this.editavel = true;
			jQuery('#dialogCreateSalaSimplificada').modal('hide');
		}).catch(response => {
			this.editavel = true;
			//this.erroAviso = true;
			this.aviso = this.erroHttp(response);
			this.status = this.ERROR;
			alert(this.aviso);
		})
	}

	toggleModoLista () {
		this.modoLista = !this.modoLista;
	}

	criarListaDisciplinasCurso () {
		if (confirm("Deseja adicionar todas as disciplinas selecionadas deste curso?")) {
			this.selecionaPLDA();
			this.editavel = false;
			this.salaSimplificadaService.createAll(this.plDisciplinasAcademicosSelecionadoList)
				.then (response => {
					this.editavel = true;
					jQuery('#dialogBuscaPLDA').modal('hide');
					jQuery('#dialogCreateSalaSimplificada').modal('hide');
				}).catch(response => {
					this.editavel = true;
					//this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					this.status = this.ERROR;
					alert(this.aviso);
				})
		}
	}

	alterar () {
		this.editavel = false;
		this.salaSimplificadaService.update(SalaSimplificada.generatePostSalaSimplificada(
			this.salaSimplificadaTemp.id,
			this.plDisciplinasAcademicosSelecionado,
			this.salaMoodleId,
			this.periodoLetivosService.periodoLetivosIdIndex.get(this.periodoLetivoSelecionadoId),
			this.loteSalasSimplificadoService.loteSalasSimplificadoSelecionada.id,
			<Usuario>this.salaSimplificadaTemp.professor,
			this.salaMoodleId ? this.linkMoodle : ''
			)
		).then (response => {
			this.editavel = true;
			jQuery('#dialogCreateSalaSimplificada').modal('hide');
		}).catch(response => {
			this.editavel = true;
			//this.erroAviso = true;
			this.aviso = this.erroHttp(response);
			this.status = this.ERROR;
			alert(this.aviso);
		})
	}

	refreshSala () {
		if (!confirm("Deseja buscar e atualizar com informações mais recentes esta sala?"))
			return;
		this.editavel = false;
		this.salaSimplificadaService.refreshSala(this.salaSimplificadaTemp.id)
		.then (response => {
			this.editavel = true;
			jQuery('#dialogCreateSalaSimplificada').modal('hide');
		}).catch(response => {
			this.editavel = true;
			//this.erroAviso = true;
			this.aviso = this.erroHttp(response);
			this.status = this.ERROR;
			alert(this.aviso);
		})
	}

	novo () {
		this.limpar();
		this.salaSimplificadaTemp = SalaSimplificada.generate();
		this.plDisciplinasAcademicosTemp = PlDisciplinasAcademicos.generatePlDisciplinasAcademicos();
		this.periodoLetivoSelecionadoId = '';
		this.faculdadeSelecionadaId = '';
		this.cursoSelecionadoId = '';
		this.salaMoodleId = '';
		this.linkMoodle = '';
		this.disciplinaSelecionadaNome = '';
		this.selecionaPLDA();
		this.emCriacao = true;
	}

	selecionar(salaSimplificada : SalaSimplificada) {
		this.limpar();
        this.salaSimplificadaTemp = salaSimplificada.clone();
		this.plDisciplinasAcademicosTemp = new PlDisciplinasAcademicos(salaSimplificada.id, salaSimplificada.curso, salaSimplificada.periodo_letivo,
			salaSimplificada.nome_sala, "", salaSimplificada.disciplina_key, salaSimplificada.carga_horaria_total_disciplina, null, 
			salaSimplificada.turma_nome, salaSimplificada.turma_id, salaSimplificada.avaliacao, null);
		this.periodoLetivoSelecionadoId = '' + (<PeriodoLetivo>salaSimplificada.periodo_letivo).id;
		this.faculdadeSelecionadaId = '' //+ (<Faculdade>(<Curso>salaSimplificada.curso).faculdade).id;
		this.cursoSelecionadoId = '' //+ (<Curso>salaSimplificada.curso).id;
		this.disciplinaSelecionadaNome = '' //+ salaSimplificada.nome_sala;
		this.salaMoodleId = salaSimplificada.sala_moodle_id;
		this.linkMoodle = this.salaMoodleId ? salaSimplificada.link_moodle : '';
		this.selecionaPLDA();
        this.emCriacao = false;
    }

	limpar() {
		this.aviso = '';
		this.status = this.COMPLETE;
		this.modoLista = false;
	}

	reset() {
        this.emCriacao = false;
        this.salaSimplificadaTemp = SalaSimplificada.generate();
    }

	deletar(salaSimplificada: SalaSimplificada) {
        if (confirm("Deseja excuir esta Sala Simplificada?")) {
            this.editavel = false;
            this.salaSimplificadaService.delete(salaSimplificada.id)
				.then (response => {
					this.salaSimplificadaTemp = SalaSimplificada.generate();
					this.editavel = true;
					this.emCriacao = false;
				})
				.catch(response => {
					//this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					this.status = this.ERROR;
					alert(this.aviso);
				})
        }
        
    }

	exportarEstudantes(salaSimplificada:SalaSimplificada) {
        if (!confirm("Deseja inserir os estudantes nesta sala?")) 
            return;
		this.erroAviso = false;
		this.aviso = '';
        jQuery('#dialogExportResult').modal('show');
        jQuery('#saidaExport').html("<i>Aguarde...<i>");
        this.editavel = false;
        this.salaSimplificadaService.exportarEstudantes(salaSimplificada.id)
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

	preparaRestauracaoAutomatica (salaSimplificada) {
		this.editavel = false;
		this.courseImportId = "";
		this.salaSimplificadaService.getMacro(salaSimplificada.id)
            .then(response => {
        		jQuery('#dialogRestore').modal('show');
				jQuery('#saidaRestore').html('');
                this.macroTempId = response;
        		this.blockAutoRestore = false;
                this.editavel = true;
            })
            .catch(response => {
        		jQuery('#dialogRestore').modal('show');
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.editavel = true;
            });
	}
	executarRestauracaoAutomatica () {
		this.editavel = false;
        this.blockAutoRestore = true;
        this.salaSimplificadaService.executarRestauracaoSala(this.salaSimplificadaTemp.id, this.macroTempId, this.courseImportId)
            .then(response => {
                jQuery('#saidaRestore').html(response);
				this.salaSimplificadaService.list(this.salaSimplificadaTemp.id)
                this.editavel = true;
            })
            .catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.editavel = true;
                jQuery('#saidaRestore').html('<span style="color: red;">'+this.aviso+"</span>");
            });
	}

	checkSalaMoodleId() {
		return !isNaN(parseInt(this.salaMoodleId)) && parseInt(this.salaMoodleId) != 0;
	}

	ngOnInit() {

	}

}

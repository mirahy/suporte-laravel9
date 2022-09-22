import { ArrayIndexador } from '../array-indexador';
import { Curso } from '../cursos/curso';
import { PeriodoLetivo } from '../periodo-letivos/periodo-letivo';
import { Estudante } from './estudante';

export class PlDisciplinasAcademicos {
    id: number;
    curso:Curso|number;
    periodo_letivo:PeriodoLetivo|number;
    disciplina:string;
    estudantes:string|Array<Estudante> = [];
    disciplina_key;

    carga_horaria_total_disciplina;
    grupo;
    turma_nome;
    turma_id; 
    avaliacao;
    
    cpf_professor;
    periodo_letivo_key:number;
    
    constructor(id:(number|any), curso?:Curso|number, periodo_letivo?:PeriodoLetivo|number, disciplina?:string, estudantes?: string|Array<Estudante>, disciplina_key?,
            carga_horaria_total_disciplina?, grupo?: string, turma_nome?: string, turma_id?, avaliacao?: string, cpf_professor?: string, periodo_letivo_key?: number) {
        if (typeof id == "number") {
            this.id = id;
            this.curso = curso;
            this.periodo_letivo = periodo_letivo;
            this.disciplina = disciplina;
            this.estudantes = estudantes;
            this.disciplina_key = disciplina_key;
            this.carga_horaria_total_disciplina= carga_horaria_total_disciplina;
            this.grupo = grupo;
            this.turma_nome = turma_nome; 
            this.turma_id = turma_id; 
            this.avaliacao = avaliacao;
            this.cpf_professor = cpf_professor;
            this.periodo_letivo_key = periodo_letivo_key;
        }
        else {
            var pl = null;
            if (id['periodo_letivo'] instanceof PeriodoLetivo) 
                pl = id['periodo_letivo'];
            else if (id['periodo_letivo_id']){
                if (parseInt(id['periodo_letivo_id']) == id['periodo_letivo_id'])
                    pl = parseInt(id['periodo_letivo_id']);
                else
                    pl = id['periodo_letivo_id'];
            }
            else if (id['periodo_letivo']) {
                if (parseInt(id['periodo_letivo']) == id['periodo_letivo'])
                    pl = parseInt(id['periodo_letivo']);
                else
                    pl = id['periodo_letivo'];
            }
            this.id = id['id'] ? parseInt(id['id']) : 0;
            this.curso = id['curso_id'];
            this.periodo_letivo = pl;
            this.disciplina = id['disciplina'] ? id['disciplina'] : id['nome_disciplina'];
            this.estudantes = id['estudantes'] ? Estudante.converteJSONParaEstudantes (id['estudantes']) : [];
            this.disciplina_key = id['disciplina_key'] ? id['disciplina_key'] : (id['codigo_disciplina'] ? id['codigo_disciplina'] : 0);
            this.carga_horaria_total_disciplina = id['carga_horaria_total_disciplina'];
            this.grupo = id['grupo'];
            this.turma_nome = id['turma_nome']; 
            this.turma_id = id['turma_id']; 
            this.avaliacao = id['avaliacao'];
            this.cpf_professor = id['cpf_professor'];
            this.periodo_letivo_key = id['periodo_letivo_id'];
        }
    }
    
    static generateList(list):Array<PlDisciplinasAcademicos> {
        var plDisciplinasAcademicos:Array<PlDisciplinasAcademicos> = [];
        for(var i = 0; i < list.length; i++) {
            var plda = new PlDisciplinasAcademicos(list[i]);
            plDisciplinasAcademicos.push(plda);
        }
        return plDisciplinasAcademicos;
    }

    static generateListPlus(list, cursosKeyIndex:ArrayIndexador<Curso>, periodoLetivosNomeIndex:ArrayIndexador<PeriodoLetivo>):Array<PlDisciplinasAcademicos> {
        var plDisciplinasAcademicos:Array<PlDisciplinasAcademicos> = [];
        for(var i = 0; i < list.length; i++) {
            if (cursosKeyIndex)
                list[i]['curso_id'] = cursosKeyIndex.get( list[i]['codigo_curso'] );
            if (periodoLetivosNomeIndex)
                list[i]['periodo_letivo'] = periodoLetivosNomeIndex.get( list[i]['periodo_letivo'] );
            var plda = new PlDisciplinasAcademicos(list[i]);
            plDisciplinasAcademicos.push(plda);
        }
        return plDisciplinasAcademicos;
    }

    static generatePlDisciplinasAcademicos() :PlDisciplinasAcademicos {
        return new PlDisciplinasAcademicos(0,Curso.generateCurso(),PeriodoLetivo.generatePeriodoLetivo(), "", [], "", "", "", "", "", "", "", 0);
    }

    toForm() {
        return {
            id: this.id,
            curso_id: typeof this.curso == 'object' ? this.curso.id : this.curso, 
            periodo_letivo_id: typeof this.periodo_letivo == 'object' ? this.periodo_letivo.id : this.periodo_letivo,
            disciplina: this.disciplina,
            //estudantes: this.estudantes ? Estudante.converteEstudantesParaJSON(<Array<Estudante>>this.estudantes) : "",
            disciplina_key: this.disciplina_key,
            carga_horaria_total_disciplina: this.carga_horaria_total_disciplina,
            grupo: this.grupo,
            turma_nome: this.turma_nome,
            turma_id: this.turma_id,
            avaliacao: this.avaliacao,
            cpf_professor: this.cpf_professor
        }
    }

    toString() {
        if (typeof this.periodo_letivo == 'object' && this.periodo_letivo instanceof PeriodoLetivo)
            return this.periodo_letivo.id_sigecad;
        return this.periodo_letivo;
    }
    
    clone() {
        return new PlDisciplinasAcademicos(this.id, this.curso, this.periodo_letivo, this.disciplina, this.estudantes, this.disciplina_key, this.carga_horaria_total_disciplina, this.grupo, this.turma_nome, this.turma_id, this.avaliacao, this.cpf_professor, this.periodo_letivo_key);
    }
}
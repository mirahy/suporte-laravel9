import { ArrayIndexador } from "../array-indexador";
import { Curso } from "../cursos/curso";
import { LoteSalasSimplificado } from "../lote-salas-simplificado/lote-salas-simplificado";
import { PeriodoLetivo } from "../periodo-letivos/periodo-letivo";
import { PlDisciplinasAcademicos } from "../pl-disciplinas-academicos/pl-disciplinas-academicos";
import { Usuario } from "../usuarios/usuario";

export class SalaSimplificada {
    
    id: number;
    nome_sala:string;
    professor: Usuario|string;
    curso: Curso|number;
    periodo_letivo_key: number;
    disciplina_key: string;
    periodo_letivo: PeriodoLetivo|number;
    turma_id: string;
    turma_nome: string;
    carga_horaria_total_disciplina: string;
    avaliacao: string;
    sala_moodle_id: number;
    link_moodle:string;
    lote: LoteSalasSimplificado|number;

    
	constructor(id:(number|any), nome_sala?:string, professor?:Usuario|string, curso?: Curso|number, periodo_letivo_key?: number,
            disciplina_key?: string, periodo_letivo?: PeriodoLetivo|number, turma_id?: string, turma_nome?: string, 
            carga_horaria_total_disciplina?: string, avaliacao?:string, sala_moodle_id?: number, link_moodle?:string, lote?: LoteSalasSimplificado|number) {
        if (typeof id == "number") {
            this.id = id;
            this.nome_sala = nome_sala;
            this.professor = professor;
            this.curso = curso;
            this.periodo_letivo_key = periodo_letivo_key;
            this.disciplina_key = disciplina_key;
            this.periodo_letivo = periodo_letivo;
            this.turma_id = turma_id;
            this.turma_nome = turma_nome;
            this.carga_horaria_total_disciplina = carga_horaria_total_disciplina;
            this.avaliacao = avaliacao;
            this.sala_moodle_id = sala_moodle_id;
            this.link_moodle = link_moodle;
            this.lote = lote;
        }
        else {
            this.id = id['id'];
            this.nome_sala = id['nome_sala'];
            this.professor = id['professor'];
            this.curso = id['curso_id'];
            this.periodo_letivo_key = id['periodo_letivo_key'];
            this.disciplina_key = id['disciplina_key'];
            this.periodo_letivo = id['periodo_letivo_id'];
            this.turma_id = id['turma_id'];
            this.turma_nome = id['turma_nome'];
            this.carga_horaria_total_disciplina = id['carga_horaria_total_disciplina'];
            this.avaliacao = id['avaliacao'];
            this.sala_moodle_id = id['sala_moodle_id'];
            this.link_moodle = id['link_moodle'];
            this.lote = id['lote_id'];
        }
    }
    
    static generate () {
        return new SalaSimplificada(0,"");
    }

    static generateList(list:Array<any>) : Array<SalaSimplificada> {
        var salas:Array<SalaSimplificada> = [];
        for(var i = 0; i < list.length; i++) {
            var sala = new SalaSimplificada(list[i]);
            salas.push(sala);
        }
        return salas;
    }

    static generateListPlus(list:Array<any>, cursosIndex: ArrayIndexador<Curso>, periodoletivosIndex: ArrayIndexador<PeriodoLetivo>, loteSelecionado:LoteSalasSimplificado) : Array<SalaSimplificada> {
        var salas:Array<SalaSimplificada> = [];
        for(var i = 0; i < list.length; i++) {
            var sala = new SalaSimplificada(list[i]);
            var temp = null;
            if (cursosIndex) {
                temp = cursosIndex.get(sala.curso);
                if (temp)
                    sala.curso = temp.clone();
            }
            if (periodoletivosIndex) {
                temp = periodoletivosIndex.get(sala.periodo_letivo);
                if (temp)
                    sala.periodo_letivo = temp.clone();
            }
            if (loteSelecionado.id == sala.lote)
                sala.lote = loteSelecionado;
            sala.professor = sala.professor ? new Usuario(sala.professor['id'], sala.professor['name'], sala.professor['email']) : null;
            salas.push(sala);
        }
        return salas;
    }

    static generatePostSalaSimplificada (id:number, plda: PlDisciplinasAcademicos, sala_moodle_id, periodo_letivo:PeriodoLetivo, lote_id:number, professor?: Usuario, link_moodle?:string) {
        var periodo_letivo_key = plda.periodo_letivo_key;
        if (!periodo_letivo_key)
            periodo_letivo_key = plda.periodo_letivo instanceof PeriodoLetivo ? plda.periodo_letivo.id_sigecad : (typeof plda.periodo_letivo == 'number' ? plda.periodo_letivo : null);
        //var periodo_letivo_nome = plda.periodo_letivo instanceof PeriodoLetivo ? plda.periodo_letivo.nome : (typeof plda.periodo_letivo == 'string' ? plda.periodo_letivo : null);
        return {
            id: id,
			nome_sala: plda.disciplina,
			cpf_professor: plda.cpf_professor,
            professor_id: professor ? professor.id : null,
			periodo_letivo_key: periodo_letivo_key,
            periodo_letivo_nome: periodo_letivo.nome,
			curso_id: (<Curso>plda.curso).id,
			disciplina_key: plda.disciplina_key,
			turma_id: plda.turma_id,
			turma_nome: plda.turma_nome,
            carga_horaria_total_disciplina: plda.carga_horaria_total_disciplina,
            avaliacao: plda.avaliacao,
			sala_moodle_id: sala_moodle_id,
            link_moodle: link_moodle,
			lote_id: lote_id,
        }
    }
    
    clone() {
        return new SalaSimplificada(this.id, this.nome_sala, this.professor, this.curso, this.periodo_letivo_key, this.disciplina_key, this.periodo_letivo, 
            this.turma_id, this.turma_nome, this.carga_horaria_total_disciplina, this.avaliacao, this.sala_moodle_id, this.link_moodle, this.lote);
    }
}
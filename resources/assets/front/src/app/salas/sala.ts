import { Status } from '../status';
import { Curso } from '../cursos/curso';
import { ArrayIndexador } from '../array-indexador';

export class Sala {

    id: number;
    email: string;
    curso;
    mensagem: string;
    nome_professor: string;
    nome_sala: string;
    modalidade: string;
    objetivo_sala: string;
    observacao: string;
    link_backup_moodle: string;
    senha_aluno: string;
    status: Status;
    estudantes: string;

    periodo_letivo_id = "";

    carga_horaria_total_disciplina = "";
    turma_nome = "";
    avaliacao = "";
    turma_id = "";
    periodo_letivo_key = "";
    curso_key = "";
    disciplina_key = "";

    solicitante_id = "";
    macro_id = 0;
    sala_moodle_id = 0;
    lote_salas_id = 0;

    username_professor = "";
    cpf_professor = "";

    created_at: Date;

    public constructor (id: number, curso, email: string, mensagem: string, nome_professor: string, nome_sala: string, modalidade: string, objetivo_sala: string,
        observacao: string, link_backup_moodle :string, senha_aluno: string, status: Status, estudantes: string, periodo_letivo_id, macro_id, sala_moodle_id, created_at: Date){

        this.id = id;
        this.curso = curso;
        this.email = email;
        this.mensagem = mensagem;
        this.nome_professor = nome_professor;
        this.nome_sala = nome_sala;
        this.modalidade = modalidade;
        this.objetivo_sala = objetivo_sala;
        this.observacao = observacao;
        this.link_backup_moodle = link_backup_moodle;
        this.senha_aluno = senha_aluno;
        this.status = status;
        this.estudantes = estudantes;
        this.periodo_letivo_id = periodo_letivo_id ? periodo_letivo_id : "";
        this.macro_id = macro_id
        this.sala_moodle_id = sala_moodle_id;
        this.created_at = created_at;
    }

    public static generateList (list:Array<any>):Array<Sala> {
        var salaList: Array<Sala> = [];
        list.forEach(salaAny => {
            var sala = this.generateSala (salaAny)
            salaList.push(sala);
        });
        return salaList;
    };
    public static generateListPlus (list:Array<any>, cursosIndex:ArrayIndexador<Curso>):Array<Sala> {
        var salaList: Array<Sala> = [];
        list.forEach(salaAny => {
            salaAny.curso = cursosIndex.get(salaAny.curso_id);
            var sala = this.generateSala (salaAny)
            salaList.push(sala);
        });
        return salaList;
    };
    public static generateSala (salaAny):Sala {
        var sala = new Sala(salaAny.id,
            salaAny.curso,
            salaAny.email,
            salaAny.mensagem,
            salaAny.nome_professor,
            salaAny.nome_sala,
            salaAny.modalidade,
            salaAny.objetivo_sala,
            salaAny.observacao,
            salaAny.link_backup_moodle,
            salaAny.senha_aluno,
            Status.generateStatus (salaAny.status),
            salaAny.estudantes,
            salaAny.periodo_letivo_id,
            salaAny.macro_id,
            salaAny.sala_moodle_id,
            new Date(salaAny.created_at + " GMT")
        )
        sala.carga_horaria_total_disciplina = salaAny.carga_horaria_total_disciplina ? salaAny.carga_horaria_total_disciplina : "";
        sala.turma_nome = salaAny.turma_nome ? salaAny.turma_nome : "";
        sala.avaliacao = salaAny.avaliacao ? salaAny.avaliacao : "";
        sala.turma_id = salaAny.turma_id ? salaAny.turma_id : "";
        sala.periodo_letivo_key = salaAny.periodo_letivo_key ? salaAny.periodo_letivo_key : "";
        sala.curso_key = salaAny.curso_key ? salaAny.curso_key : "";
        sala.disciplina_key = salaAny.disciplina_key ? salaAny.disciplina_key : "";
        return sala;
    }

    public static geraNovaSala() {
        return new Sala(0,"","","","","","","","","","",Status.novoStatus(),"","",0,0,new Date());
    }

    public clone() {
        return Sala.generateSala(this);
    }
}

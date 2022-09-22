import { Status } from '../status';

export class SalaOld {

    id: number;
    curso: string;
    email: string;
    faculdade: string;
    mensagem: string;
    nome_professor: string;
    nome_sala: string;
    modalidade: string;
    objetivo_sala: string;
    observacao: string;
    senha_aluno: string;
    senha_professor: string;
    status: Status;
    created_at: Date;

    public constructor (id: number, curso: string, email: string, faculdade: string, mensagem: string, nome_professor: string, 
        nome_sala: string, modalidade: string, objetivo_sala: string, observacao: string, senha_aluno: string, senha_professor: string, status: Status, created_at: Date,){

        this.id = id;
        this.curso = curso;
        this.email = email;
        this.faculdade = faculdade;
        this.mensagem = mensagem;
        this.nome_professor = nome_professor;
        this.nome_sala = nome_sala;
        this.modalidade = modalidade;
        this.objetivo_sala = objetivo_sala
        this.observacao = observacao;
        this.senha_aluno = senha_aluno;
        this.senha_professor = senha_professor;
        this.status = status;
        this.created_at = created_at;

    }

    public static generateList (list:Array<any>):Array<SalaOld> {
        var salaList: Array<SalaOld> = [];
        list.forEach(salaAny => {
            var sala = this.generateSala (salaAny)
            salaList.push(sala);
        });
        return salaList;
    };
    public static generateSala (salaAny):SalaOld {
        return new SalaOld(salaAny.id,
            salaAny.curso,
            salaAny.email,
            salaAny.faculdade,
            salaAny.mensagem,
            salaAny.nome_professor,
            salaAny.nome_sala,
            salaAny.modalidade,
            salaAny.objetivo_sala,
            salaAny.observacao,
            salaAny.senha_aluno,
            salaAny.senha_professor,
            Status.generateStatus (salaAny.status),
            new Date(salaAny.created_at + " GMT")
        )
    }

    public clone() {
        return SalaOld.generateSala(this);
    }
}
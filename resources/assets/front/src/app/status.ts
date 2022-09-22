export class Status {

    static readonly CHAVES = {
        ANALISE : 'ANALISE',
        CONCLUIDO : 'CONCLUIDO',
        REJEITADO : 'REJEITADO',
        DEFERIDO : 'DEFERIDO',
        INDEFERIDO : 'INDEFERIDO',
        CANCELADO : 'CANCELADO',
        PROCESSO : 'PROCESSO',
    }

    id: number;
    chave: string;
    descricao: string; 
    cor: string;

    public constructor(id:number, chave: string, descricao: string, cor: string) {
        this.id = id;
        this.chave = chave,
        this.descricao = descricao;
        this.cor = cor;
    }

    public static generateStatus(st:any) : Status{
        if (!st)
            return this.novoStatus();
        return new Status (st.id, st.chave, st.descricao,st.cor);
    }

    static generateList(list) {
        var statusList:Array<Status> = [];
        for(var i = 0; i < list.length; i++) {
            var st = Status.generateStatus(list[i]);
            statusList.push(st);
        }
        return statusList;
    }

    public static novoStatus() :Status {
        return new Status(0,"","","");
    }
}
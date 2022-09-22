import { Usuario } from '../usuarios/usuario';
import { Status } from '../status';

export class EventoReserva {

    private static readonly NOW = new Date();

    private static readonly COR_ANALISE = "#ffd700";
    private static readonly COR_ANALISE_OUTRO = "#dadada";
    private static readonly COR_REJEITADO = "#ff5050";
    private static readonly COR_DEFERIDO = "#00b900";
    private static COR = EventoReserva.COR_ANALISE;


    public static defaultDateStart:Date = new Date(EventoReserva.NOW.getFullYear(), EventoReserva.NOW.getMonth(), EventoReserva.NOW.getDate(), 0);
    public static defaultDateEnd:Date = new Date(EventoReserva.NOW.getFullYear(), EventoReserva.NOW.getMonth(), EventoReserva.NOW.getDate(), 23, 30);

    id:number = 0;
    allDay:boolean = false;
    start:Date;
    end:Date;
    title:string = "";
    backgroundColor:string = EventoReserva.COR;
    borderColor:string
    textColor:string

    maisDay:boolean = false;
    repeticao:boolean = false;
    horaStart:Date;
    horaEnd:Date;

    usuario:Usuario;
    observacao:string;
    justificativa:string;
    status:Status = new Status(0,"","","");

    created_at:Date;
    updated_at:Date;
    /*
    groupIdurl:string;
    classNames:Array<string> = []
    editable:boolean;
    startEditable:boolean;
    durationEditable:boolean;
    resourceEditable:boolean;
    rendering:string; //('background|inverse-background');
    overlap:boolean
    constraint
    extendedProps
    source*/

    constructor() {
        this.start = EventoReserva.defaultDateStart;
        this.end = EventoReserva.defaultDateEnd;
        this.horaStart = EventoReserva.defaultDateStart;
        this.horaEnd = EventoReserva.defaultDateEnd;
    }

    gerarEventApi(id?:number) {
        var eventApi:any = {title: this.title, allDay: this.allDay, maisDay: this.maisDay}
        if (id) 
            eventApi.id = id;
        if (this.allDay) {
            if (this.maisDay) {
                eventApi.start = this.start.toJSON().slice(0,10);
                eventApi.end = this.end.toJSON().slice(0,10);
                var end = new Date(this.end);
                end.setDate(this.end.getDate()+1);
                eventApi.end = end.toJSON().slice(0,10);
            }
            else {
                eventApi.start = this.start.toJSON().slice(0,10);
                eventApi.end = null;
            }
        }
        else {
            if (this.maisDay) {
                eventApi.start = new Date(this.start.getFullYear(), this.start.getMonth(), this.start.getDate(), this.horaStart.getHours(), this.horaStart.getMinutes()).toJSON();
                eventApi.end = new Date(this.end.getFullYear(), this.end.getMonth(), this.end.getDate(), this.horaEnd.getHours(), this.horaEnd.getMinutes()).toJSON();
            }
            else {
                eventApi.start = new Date(this.start.getFullYear(), this.start.getMonth(), this.start.getDate(), this.horaStart.getHours(), this.horaStart.getMinutes()).toJSON();
                eventApi.end = new Date(this.start.getFullYear(), this.start.getMonth(), this.start.getDate(), this.horaEnd.getHours(), this.horaEnd.getMinutes()).toJSON();
            }
        } 
        if (this.backgroundColor != EventoReserva.COR)
            eventApi.backgroundColor = this.backgroundColor;
        return eventApi;
    }

    static obtemEvento(ev, evIndex) {
        var evento:EventoReserva = new EventoReserva();
        evento.id = ev.id ? parseInt(ev.id) : 0;
        evento.allDay = ev.allDay;
        if (ev.allDay) {
            evento.start = new Date(ev.start);
            evento.horaStart = new Date(ev.start);
            evento.horaStart.setHours(0,0,0);
            evento.horaEnd = new Date(ev.start);
            evento.horaEnd.setHours(23,30,0);
            if (ev.end) {
                // checar se é no mesmo dia
                var check = new Date(ev.end)
                check.setDate(check.getDate()-1);
                if (evento.horaStart.getTime() >= check.getTime() ) {
                    evento.maisDay = false;
                    evento.end = null;
                }
                else {
                    evento.maisDay = true;
                    evento.end = new Date(check);
                }
                
            }
            else {
                evento.maisDay = false;
                evento.end = null;
            }
            // tornar "novo evento" com allDay setado false
            //if (evento.id == 0)
               // evento.allDay = false;
        }
        else {
            evento.start = new Date(ev.start);
            evento.horaStart = new Date(ev.start);
            if (ev.end) {
                evento.end = new Date(ev.end);
                // tratamento continuidade
                evento.horaEnd = new Date(ev.end);
                evento.maisDay = evento.start.getDate() < evento.end.getDate();
            }
            else {
                evento.end = new Date (ev.start);
                evento.end.setHours(ev.start.getHours() + 1);
                evento.horaEnd = new Date(evento.end);
                evento.maisDay = evento.start.getDate() < evento.end.getDate();
            }
        }
        evento.title = ev.title;
        if (evIndex) {
            evento.observacao = evIndex.observacao;
            evento.status = Status.generateStatus (evIndex.status),
            evento.justificativa = evIndex.justificativa;
            evento.created_at = new Date(evIndex.created_at+" GMT");
            evento.updated_at = new Date(evIndex.updated_at+" GMT");
            evento.usuario = new Usuario(evIndex.usuario_id,"","","",null);
        }
        evento.backgroundColor = ev.backgroundColor == null || ev.backgroundColor == "" ? evento.backgroundColor = EventoReserva.COR : ev.backgroundColor;
        return evento;
        //evento.borderColor
        //evento.textColor
    }

    public gerarEventPost(id?:number|Status) {
        var eventApi:any = {title: this.title, allDay: this.allDay, maisDay: this.maisDay, observacao: this.observacao, justificativa: this.justificativa}
        if (typeof id == 'number') 
            eventApi.id = id;
        if (typeof id == 'object' && id instanceof Status)
            eventApi.status = id;
        else 
            eventApi.status = this.status;
        if (this.allDay) {
            if (this.maisDay) {
                eventApi.start = this.start.toJSON().slice(0,10);
                eventApi.end = this.end.toJSON().slice(0,10);
                var end = new Date(this.end);
                end.setDate(this.end.getDate()+1);
                eventApi.end = end.toJSON().slice(0,10);
            }
            else {
                eventApi.start = this.start.toJSON().slice(0,10);
                eventApi.end = null;
            }
        }
        else {
            if (this.maisDay) {
                eventApi.start = new Date(this.start.getFullYear(), this.start.getMonth(), this.start.getDate(), this.horaStart.getHours(), this.horaStart.getMinutes()).toJSON().slice(0, 19).replace('T', ' ');
                eventApi.end = new Date(this.end.getFullYear(), this.end.getMonth(), this.end.getDate(), this.horaEnd.getHours(), this.horaEnd.getMinutes()).toJSON().slice(0, 19).replace('T', ' ');
            }
            else {
                eventApi.start = new Date(this.start.getFullYear(), this.start.getMonth(), this.start.getDate(), this.horaStart.getHours(), this.horaStart.getMinutes()).toJSON().slice(0, 19).replace('T', ' ');
                eventApi.end = new Date(this.start.getFullYear(), this.start.getMonth(), this.start.getDate(), this.horaEnd.getHours(), this.horaEnd.getMinutes()).toJSON().slice(0, 19).replace('T', ' ');
            }
        } 
        if (this.backgroundColor != EventoReserva.COR)
            eventApi.backgroundColor = this.backgroundColor;
        return eventApi;
    }
    static obtemEventoGet(ev, usuarioLogado:Usuario) {
        ev.id = parseInt(ev.id);
        if (!ev.allDay) {
            ev.start = new Date(ev.start + " GMT");
            ev.end = new Date(ev.end + " GMT");
        }
        if (!ev.backgroundColor) {
            if (ev.status) {
                if (usuarioLogado.id != ev.usuario_id && !usuarioLogado.gestor && ev.status.chave == Status.CHAVES.ANALISE)
                    ev.backgroundColor = EventoReserva.COR_ANALISE_OUTRO
                else
                    ev.backgroundColor = ev.status.cor
            }
               
        }
        return ev;
        //evento.borderColor
        //evento.textColor
    }
}
import { Injectable } from '@angular/core';
import { EventoReserva } from './reservas/evento';
import { Http } from '@angular/http';
import { Usuario } from './usuarios/usuario';
import { DadosService } from './dados.service';
import { Recurso } from './recurso/recurso';

@Injectable()
export class ReservasService {

    evento: EventoReserva = new EventoReserva();
    events: any[] = [];
    eventsIndex = {};
    recurso:Recurso = new Recurso(0,"","");
    usuario:Usuario = new Usuario(0,"","","",false);

    constructor(private http:Http, private dadosService:DadosService) { }

    create ():Promise<any> {
        return this.http.post ("/reservas", this.evento.gerarEventPost(this.dadosService.statuses[0])).toPromise()
            .then (response => {
                var e = EventoReserva.obtemEventoGet(response.json(), this.usuario);
                this.eventsIndex[e.id] = this.events.length;
                this.events = [...this.events, 
                    //this.evento.gerarEventApi(this.events.length)
                    e
                ];
                return response;
            })
    }

    update ():Promise<any> {
        return this.http.put ("/reservas/"+this.evento.id, this.evento.gerarEventPost()).toPromise()
            .then (response => {
                var e = EventoReserva.obtemEventoGet(response.json(), this.usuario);
                var copia = this.events.slice();
                copia[this.eventsIndex[e.id]] = e;
                this.events = copia;
                return response;
            })
    }

    delete ():Promise<any> {
        return this.http.delete ("/reservas/"+this.evento.id).toPromise()
            .then (response => {
                var copia = this.events.slice();
                copia.splice(this.eventsIndex[this.evento.id], 1);
                this.events = copia;
                return response;
            })
    }

    listar() : Promise<Array<any>>{
        return this.http.get("/reservas/listar").toPromise ()
        .then(response => {
            var lista = response.json();
            this.events = [];
            this.eventsIndex = {};
            for (var i in lista) {
                var e = EventoReserva.obtemEventoGet(lista[i], this.usuario);
                this.events.push(e)
                this.eventsIndex[e.id] = i;
            }
            //console.log(lista)
            console.log(this.events)
            return this.events;
        });
    }

    usuarioLogadoRecurso(): Promise<Usuario> {
        return this.http.get("/reservas/usuario").toPromise()
            .then (response => {
                var u = new Usuario(response.json())
                this.usuario = u;
                return this.usuario;
            }); 
    }

    recursoSelecionado() {
        return this.http.get("/reservas/recurso").toPromise()
            .then (response => {
                this.recurso = new Recurso(response.json());
                return this.recurso;
            }); 
    }

    cancelaReserva(justificativa:string):Promise<any> {
        return this.http.put("/reservas/cancelar/", {reservaId: this.evento.id, justificativa: justificativa}).toPromise()
            .then(response => {
                var e = EventoReserva.obtemEventoGet(response.json(), this.usuario);
                var copia = this.events.slice();
                copia[this.eventsIndex[e.id]] = e;
                this.events = copia;
                return response;
            })
    }

    mudarStatusReserva(status:string, justificativa:string):Promise<any> {
        return this.http.put("/reservas/status/", {status: status, reservaId: this.evento.id, justificativa: justificativa}).toPromise()
            .then(response => {
                var e = EventoReserva.obtemEventoGet(response.json(), this.usuario);
                var copia = this.events.slice();
                copia[this.eventsIndex[e.id]] = e;
                this.events = copia;
                return response;
            })
    }
}

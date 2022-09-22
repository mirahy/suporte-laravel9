import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { EventoGerador } from './agenda/evento';

@Injectable()
export class AgendaService {

    evento: EventoGerador = new EventoGerador();
    events: any[] = [];
    eventsIndex = {};

    constructor(private http:Http) { }

    create ():Promise<any> {
        return this.http.post ("/agenda", this.evento.gerarEventPost()).toPromise()
            .then (response => {
                var e = EventoGerador.obtemEventoGet(response.json());
                this.eventsIndex[e.id] = this.events.length;
                this.events = [...this.events, 
                    //this.evento.gerarEventApi(this.events.length)
                    e
                ];
                return response;
            })
    }

    update ():Promise<any> {
        return this.http.put ("/agenda/"+this.evento.id, this.evento.gerarEventPost()).toPromise()
            .then (response => {
                var e = EventoGerador.obtemEventoGet(response.json());
                var copia = this.events.slice();
                copia[this.eventsIndex[e.id]] = e;
                this.events = copia;
                return response;
            })
    }

    delete () :Promise<any> {
        return this.http.delete ("/agenda/"+this.evento.id).toPromise()
            .then (response => {
                var copia = this.events.slice();
                copia.splice(this.eventsIndex[this.evento.id], 1);
                this.events = copia;
                return response;
            })
    }

    listar() : Promise<Array<any>>{
        return this.http.get("/agenda/listar").toPromise ()
        .then(response => {
            var lista = response.json();
            this.events = [];
            this.eventsIndex = {};
            for (var i in lista) {
                var e = EventoGerador.obtemEventoGet(lista[i]);
                this.events.push(e)
                this.eventsIndex[e.id] = i;
            }
            //console.log(lista)
            return this.events;
        });
    }
}

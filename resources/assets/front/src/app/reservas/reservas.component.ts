import { Component, OnInit, ViewChild } from '@angular/core';
import { SelectItem } from 'primeng/api';
//import { ThemeService } from '../theme.service';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
//import { Theme } from './theme';
import { FullCalendar } from 'primeng/fullcalendar';
import ptBrLocale from '@fullcalendar/core/locales/pt-br';
import { Redimensionavel } from '../redimensionavel';
import { DadosService } from '../dados.service';
import { ThemeService } from '../theme.service';
import { Theme } from '../agenda/theme';
import { AbstractComponent } from '../abstract-component';
import { EventApi } from '@fullcalendar/core';
import { EventoReserva } from './evento';
import { ReservasService } from '../reservas.service';
import { UsuarioService } from '../usuario.service';
import { Usuario } from '../usuarios/usuario';
import { Recurso } from '../recurso/recurso';
import { Status } from '../status';
declare var jQuery: any;

@Component({
    selector: 'app-reservas',
    templateUrl: './reservas.component.html',
    styleUrls: ['./reservas.component.less']
})
export class ReservasComponent extends AbstractComponent implements OnInit {

    readonly STATUS_INICIAL_PADRAO = Status.CHAVES.ANALISE;
    readonly STATUS_TERMINADO = Status.CHAVES.DEFERIDO;

    selectedTheme: Theme;
    data: Date = new Date();
    rangeDates: any = new Date();
    temas: SelectItem[] = [];


    options: any;

    pt: any;
    intervalGamb: any = 0;

    tamanho: number = 500;

    @ViewChild('fc')
    fc: FullCalendar;


    constructor(private themeService: ThemeService, private dadosService: DadosService, private reservasService: ReservasService) {
        super();
        /*var themes: Theme[] = Theme.getThemes();

        for (var i in themes) {
            this.temas.push({ label: themes[i].name, value: themes[i] })
            if (themes[i].name == "luna-green")
                this.selectedTheme = themes[i];
        }*/
    }
    onThemeSelect() {
        //this.themeService.setTheme(this.selectedTheme.name);
    }
    get evento() {
        return this.reservasService.evento;
    }
    set evento(evento) {
        this.reservasService.evento = evento;
    }
    get events() {
        return this.reservasService.events;
    }
    set events(events) {
        this.reservasService.events = events;
    }
    get recurso(): Recurso {
        return this.reservasService.recurso;
    }
    set recurso(recurso: Recurso) {
        this.reservasService.recurso = recurso;
    }
    get usuario ():Usuario {
        return this.reservasService.usuario;
    }
    set usuario(usuario:Usuario) {
        this.reservasService.usuario = usuario;
    }

    ngOnInit() {
        this.events = [];
        this.editavel = true;
        this.reservasService.usuarioLogadoRecurso().then(response => {
            this.usuario = response;
        });

        this.dadosService.statusList()
            .then(response => {
                this.reservasService.recursoSelecionado()
                    .then(response => {
                        this.reservasService.listar()
                            .then(response => {
                                this.status = this.COMPLETE;
                            })
                            .catch(response => {
                                this.status = this.ERROR;
                                console.log(response)
                            })
                    })
                    .catch(response => {
                        this.status = this.ERROR;
                        console.log(response)
                    })
            })
            .catch(response => {
                this.status = this.ERROR;
                console.log(response)
            })

        // Alterando o texto "Compromissos" para "Lista"
        ptBrLocale.buttonText.list = "Lista";
        this.options = {
            plugins: [timeGridPlugin, dayGridPlugin, interactionPlugin, listPlugin],
            defaultDate: new Date(),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: "timeGridWeek,dayGridMonth,listMonth" //"dayGridMonth,timeGridWeek,timeGridDay,listMonth  dayGridDay,dayGridWeek"
            },
            selectable: true,
            height: this.tamanho,
            locales: [ptBrLocale],
            locale: "pt-br",
            //editable: true,
            //deepChangeDetection: true,
            //weekends: false,
            //showNonCurrentDates: true,
            dateClick: (e) => {
                //console.log (e);
            },
            select: (e) => {
                this.evento = EventoReserva.obtemEvento(e, e.id == null ? null : this.reservasService.events[this.reservasService.eventsIndex[e.id]]);
                //jQuery("#dialogEvento").modal('show');
                //console.log(this.evento)
            },
            unselect: (e) => {
                var _this = this;
                this.intervalGamb = setTimeout(function () {
                    if (_this.evento.id)
                        true;
                    else
                        _this.resetEvento();
                }, 20);
            },
            eventClick: (e) => {
                this.evento = EventoReserva.obtemEvento(e.event, this.reservasService.events[this.reservasService.eventsIndex[e.event.id]]);
                jQuery("#dialogEvento").modal('show');
                //this.modifyProp(0, "teste")
                //e.event.date = new Date()
                //e.el.style.borderColor = 'red';
                //console.log(this.evento)
            }
        };
        var _this = this;
        setTimeout(function () {
            _this.resize()
        }, 10);

        this.pt = {
            firstDayOfWeek: 0,
            dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            dayNamesMin: ['Do', 'Se', 'Te', 'Qu', 'Qu', 'Se', 'Sa'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho',
                'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            today: 'Hoje',
            clear: 'Limpar'
        };
    }
    aee(event) {
        event.jsEvent.preventDefault(); // don't let the browser navigate

        console.log("event")
    }
    modifyProp(eventIndex, newTitle) {
        let calendarEvents = this.events.slice(); // a clone
        let singleEvent = Object.assign({}, calendarEvents[eventIndex]); // a clone
        singleEvent.title = newTitle;
        calendarEvents[eventIndex] = singleEvent;
        this.events = calendarEvents; // reassign the array
        //.fullCalendar( ‘renderEvent’, event [, stick ] )
    }

    resetEvento() {
        this.evento = new EventoReserva();
    }

    changeAllDay() {
        /*var nextDay = new Date(day);
        nextDay.setDate(day.getDate()+1);*/
    }
    changeMaisDay() {
        if (this.evento.maisDay) {
            this.evento.end = new Date(this.evento.start);
            this.evento.end.setDate(this.evento.start.getDate() + 1);
        }
    }

    addEvent() {
        if (!this.evento.title) {
            alert("Insira um Título");
            document.getElementById('tituloInput').focus();
            return;
        }
        if ((this.evento.end ? this.evento.start.getTime() > this.evento.end.getTime() : false) || (this.evento.horaEnd ? this.evento.horaStart.getTime() > this.evento.horaEnd.getTime() : false)) {
            alert("O início não pode ser depois do fim");
            return;
        }

        if (this.evento.id) {
            if (confirm("Confirmar alteração deste Evento?")) {
                this.reservasService.update()
                    .then(response => {
                        jQuery("#dialogEvento").modal('hide');
                    })
                    .catch(response => {
                        alert("Falha na criação do evento!");
                    });
            }
        }
        else {
            this.reservasService.create()
                .then(response => {
                    jQuery("#dialogEvento").modal('hide');
                })
                .catch(response => {
                    alert("Falha na criação do evento!");
                });
        }

    }
    delEvent(e) {
        if (this.evento.id > 0 && confirm("Confirmar exclusão deste Evento?"))
            this.reservasService.delete().then(response => {
                jQuery("#dialogEvento").modal('hide');
            })
    }

    deferir() {
        var justificativa = prompt("Insira algum comentário para o solicitante (Opcional):")
        justificativa = justificativa == "" ? null : justificativa;
        this.reservasService.mudarStatusReserva("DEFERIDO", justificativa)
            .then(response => {
                jQuery('#dialogEvento').modal('hide');
            })
            .catch(response => {
                alert("Falha na atualização do Status da Reserva!");
            });
    }

    indeferir() {
        var justificativa = prompt("Informe uma justificativa para rejeitar esta reserva:")
        if (!justificativa) {
            alert("Uma justificativa precisa ser informada");
            return;
        }
        this.reservasService.mudarStatusReserva("INDEFERIDO", justificativa)
            .then(response => {
                jQuery('#dialogEvento').modal('hide');
            })
            .catch(response => {
                alert("Falha na atualização do Status da Reserva!");
            });
    }
    cancelarReserva() {
        var justificativa = prompt("Informe uma justificativa para cancelar esta reserva:")
        if (!justificativa) {
            alert("Uma justificativa precisa ser informada");
            return;
        }
        this.reservasService.cancelaReserva(justificativa)
            .then(response => {
                jQuery('#dialogEvento').modal('hide');
            })
            .catch(response => {
                alert("Falha no cancelamento da Reserva!");
            });
    }

    update() {
        //incorrect
        /*this.events.push({
            "title": "Conference",
            "start": "2019-06-11",
            "end": "2019-06-13"
        });*/

        //correct
        /*this.events = [...this.events, {
            "title": "Conference",
            "start": "2019-06-12",
            "end": "2019-06-14"
        }];*/
        this.events = [...this.events, {
            "title": "Conference",
            "date": this.data.toJSON()
        }];
        //console.log (this.fc)

        //incorrect
        //this.options.weekends = false;

        //correct
        //this.options = [...this.options, { weekends: false }];
        //this.options = {...this.options, { height: 600 }};
    }
    janela() {
        this.editavel = true;
        if (this.intervalGamb) {
            clearTimeout(this.intervalGamb);
        }
        //this.evento = new EventoReserva();
        setTimeout(function () {
            document.getElementById('tituloInput').focus();
        }, 500);
    }
    resize() {

        var redimensionaAgenda = function (parametros) {
            parametros.componente.calendar.setOption('height', parametros.tamanho);
        }
        var red: Redimensionavel = new Redimensionavel(null, 252, 450, null, { funcao: redimensionaAgenda, parametros: { componente: this.fc, tamanho: this.tamanho } });
        this.dadosService.componentesPersonalizados.push(red);
        this.dadosService.resizeFull();
    }

}

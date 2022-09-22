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
import { AbstractComponent } from '../abstract-component';
import { AgendaService } from '../agenda.service';
import { Theme } from '../agenda/theme';
import { EventoGerador } from '../agenda/evento';
declare var jQuery: any;

@Component({
    selector: 'app-calendario',
    templateUrl: './calendario.component.html',
    styleUrls: ['./calendario.component.less']
})
export class CalendarioComponent extends AbstractComponent implements OnInit {

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


    constructor(private themeService: ThemeService, private dadosService: DadosService, private agendaService: AgendaService) {
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
        return this.agendaService.evento;
    }
    set evento(evento) {
        this.agendaService.evento = evento;
    }
    get events() {
        return this.agendaService.events;
    }
    set events(events) {
        this.agendaService.events = events;
    }


    ngOnInit() {
        this.events = [];
        this.editavel = true;
        this.agendaService.listar().then(response => {

        });

        // Alterando o texto "Compromissos" para "Lista"
        ptBrLocale.buttonText.list = "Lista";
        this.options = {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
            defaultDate: new Date(),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: "dayGridMonth,timeGridWeek,listMonth" //"dayGridMonth,timeGridWeek,timeGridDay,listMonth  dayGridDay,dayGridWeek"
            },
            height: this.tamanho,
            locales: [ptBrLocale],
            locale: "pt-br",
            //editable: true,
            //deepChangeDetection: true,
            //weekends: false,
            //showNonCurrentDates: true,
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

    resetEvento() {
        this.evento = new EventoGerador();
    }

    resize() {

        var redimensionaAgenda = function (parametros) {
            parametros.componente.calendar.setOption('height', parametros.tamanho);
        }
        var red: Redimensionavel = new Redimensionavel(null, 0, 450, null, { funcao: redimensionaAgenda, parametros: { componente: this.fc, tamanho: this.tamanho } });
        this.dadosService.componentesPersonalizados.push(red);
        this.dadosService.resizeFull();
    }

}

import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from '../abstract-component';
import { LogsService } from '../logs.service';

@Component({
	selector: 'app-logs',
	templateUrl: './logs.component.html',
	styleUrls: ['./logs.component.less']
})
export class LogsComponent extends AbstractComponent implements OnInit {

	constructor(private logsService:LogsService) {
		super();
	}

	logsExportsEstudantes: Array<any> = []

	get pathLogsExportacaoEstudantes () {
		return this.logsService.PATH_LOGS_EXPORTACOES_ESTUDANTES;
	}

	ngOnInit() {
		this.logsService.listarLogsExportEstudantes()
			.then(response => {
				this.logsExportsEstudantes = response;
			})
			.catch(response => {
				this.status = this.ERROR;
				console.log(response)
			})
	}

}

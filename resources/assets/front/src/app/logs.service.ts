import { Injectable } from '@angular/core';
import { Http } from '@angular/http';

@Injectable()
export class LogsService {

	constructor(private http:Http) { }

	public readonly PATH_LOGS_EXPORTACOES_ESTUDANTES = "/logs/exportacao-estudantes";

	listarLogsExportEstudantes() {
		return this.http.get(this.PATH_LOGS_EXPORTACOES_ESTUDANTES)
			.toPromise()
			.then(response => {
				return response.json();
			});
	}
}

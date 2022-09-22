import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { ArrayIndexador } from './array-indexador';
import { GrupoLotesSimplificado } from './lote-salas-simplificado/grupo-lotes-simplificado';
import { LoteSalasSimplificado } from './lote-salas-simplificado/lote-salas-simplificado';
import { ServidoresMoodleService } from './servidores-moodle.service';
import { SuperMacroService } from './super-macro.service';

@Injectable()
export class LoteSalasSimplificadoService {


	loteSalasSimplificados: Array<LoteSalasSimplificado> = [];
	loteSalasSimplificadosIndex: ArrayIndexador<LoteSalasSimplificado> = null;
	loteSalasSimplificadoSelecionada: LoteSalasSimplificado = LoteSalasSimplificado.generate();

    grupoLotesSelecionado: GrupoLotesSimplificado = GrupoLotesSimplificado.generate();

	constructor(private http: Http, private superMacrosService:SuperMacroService, private servidoresMoodleService:ServidoresMoodleService) { }

	listar() {
		return this.http.get("/lote-salas-simplificados/all/" + this.grupoLotesSelecionado.id)
			.toPromise()
			.then(response => {
				this.loteSalasSimplificados = LoteSalasSimplificado.generateListPlus(response.json(), this.superMacrosService.superMacrosIndex, this.servidoresMoodleService.servidoresMoodleIndex);
				this.loteSalasSimplificadosIndex = new ArrayIndexador<LoteSalasSimplificado>(this.loteSalasSimplificados);
				return this.loteSalasSimplificados;
			});
	}

	create(loteSalasSimplificado:LoteSalasSimplificado) {
        return this.http.post("/lote-salas-simplificados",loteSalasSimplificado.getPost())
            .toPromise()
            .then(response => {
                return this.listar();
            });
    }
    update(loteSalasSimplificado:LoteSalasSimplificado) {
        return this.http.put("/lote-salas-simplificados/"+loteSalasSimplificado.id,loteSalasSimplificado.getPost())
            .toPromise()
            .then(response => {
                return this.listar();
            });
    }
    delete(loteSalasSimplificado:LoteSalasSimplificado) {
        return this.http.delete("/lote-salas-simplificados/"+loteSalasSimplificado.id)
            .toPromise()
            .then(response => {
                return this.listar();
            });
    }

    executaExportacoes(loteSalasSimplificado:LoteSalasSimplificado) {
        return this.http.get("/lote-salas-simplificados/exportacao/"+loteSalasSimplificado.id)
            .toPromise()
            .then(response => {
                return response.text();
            });
    }

    exportarEstudantes(loteSalasSimplificado:LoteSalasSimplificado) {
        return this.http.get("/lote-salas-simplificados/estudantes/"+loteSalasSimplificado.id)
            .toPromise()
            .then(response => {
                return response.text();
            });
    }
}

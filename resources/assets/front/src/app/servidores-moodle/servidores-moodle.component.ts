import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from '../abstract-component';
import { ServidoresMoodleService } from '../servidores-moodle.service';
import { ServidorMoodle } from './servidor-moodle';
declare var jQuery: any;

@Component({
	selector: 'app-servidores-moodle',
	templateUrl: './servidores-moodle.component.html',
	styleUrls: ['./servidores-moodle.component.less']
})
export class ServidoresMoodleComponent extends AbstractComponent implements OnInit {

	constructor(private servidoresMoodleService: ServidoresMoodleService) {
		super();
	}

	get servidoresMoodle() {
		return this.servidoresMoodleService.servidoresMoodle;
	}

	servidorMoodle: ServidorMoodle = ServidorMoodle.generateServidorMoodle();

	novoServidorMoodle() {
		this.servidorMoodle = ServidorMoodle.generateServidorMoodle();
		this.aviso = "";
		this.erroAviso = false;
		//this.carregarListaSigecad();
	}

	criaAlteraServidorMoodle(ev) {
		ev.preventDefault();
		var servidoresMoodleForm = jQuery('#servidoresMoodleForm')[0];
		if (servidoresMoodleForm.reportValidity()) {
			this.servidoresMoodleService.createUpdate(this.servidorMoodle)
				.then(r => {
					jQuery('#dialogCreateSM').modal('hide');

				}).catch(response => {
					this.erroAviso = true;
					this.aviso = this.erroHttp(response);
				});
		}
	}

	selecionaServidorMoodle(sm: ServidorMoodle) {
		this.aviso = "";
		this.erroAviso = false;
		this.servidorMoodle = sm.clone();
	}

	removeServidorMoodle(sm: ServidorMoodle) {
		if (confirm("Confirmar Exclusão deste Servidor Moodle")) {
			this.servidoresMoodleService.delete(sm)
				.then(r => {

				}).catch(response => {
					this.erroAviso = true;
					this.aviso = this.erroHttp(response);
					alert(this.aviso);
				});
		}
	}


	ngOnInit() {
		this.servidoresMoodleService.getServidoresMoodle()
			.then(r => {
				this.status = this.COMPLETE;
				this.editavel = true;
			}).catch(response => {
				this.erroAviso = true;
				this.aviso = this.erroHttp(response);
				this.status = this.ERROR;
				alert(this.aviso);
			});
	}

}

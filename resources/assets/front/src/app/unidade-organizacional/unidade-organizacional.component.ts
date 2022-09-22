import { Component, OnInit } from '@angular/core';
import { AbstractComponent } from '../abstract-component';
import { UnidadeOrganizacionalService } from '../unidade-organizacional.service';
import { UnidadeOrganizacional } from './unidade-organizacional';
declare var jQuery: any;

@Component({
	selector: 'app-unidade-organizacional',
	templateUrl: './unidade-organizacional.component.html',
	styleUrls: ['./unidade-organizacional.component.less']
})
export class UnidadeOrganizacionalComponent extends AbstractComponent implements OnInit {

	constructor(private unidadeOrganizacionalService: UnidadeOrganizacionalService) {
		super();
	}

	ouTemp = UnidadeOrganizacional.generate();
    ouCadastroTemp = "";

	get unidadesOrganizacionais () {
		return this.unidadeOrganizacionalService.unidadesOrganizacionais;
	}
    get ouDirRoot() {
        return this.unidadeOrganizacionalService.ouDirRoot;
    }

	criaAlteraOU(ev){
        ev.preventDefault();
        var ouForm = jQuery('#ouForm')[0];
		if (ouForm.reportValidity()) {
			if (this.ouTemp.id) {
				this.unidadeOrganizacionalService.update(this.ouTemp)
					.then(r => {
						jQuery('#dialogCreate').modal('hide');
						
					}).catch(response => {
						this.erroAviso = true;
						this.aviso = this.erroHttp(response);
					});
			}
			else {
				this.unidadeOrganizacionalService.create(this.ouTemp)
					.then(r => {
						jQuery('#dialogCreate').modal('hide');
						
					}).catch(response => {
						this.erroAviso = true;
						this.aviso = this.erroHttp(response);
					});
			}
		}
    }

	selecionaOU(ou:UnidadeOrganizacional) {
		this.ouTemp = ou.clone();
	}
	removeOU() {
		if (confirm("Confirmar Exclusão desta Unidade Organizacional?")) {
            this.unidadeOrganizacionalService.delete(this.ouTemp)
                .then(r => {
                                        
                }).catch(response => {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                    alert(this.aviso);
                });
        }
	}
	novaOU(){
		this.ouTemp = UnidadeOrganizacional.generate();
	}

	preparaAlterarOuDir() {
        this.ouCadastroTemp = this.ouDirRoot;
        jQuery('#dialogUpdateOuDir').modal('show');
    }

    alterarOuDir () {
        this.editavel = false;
        this.unidadeOrganizacionalService.setOuDirRoot(this.ouCadastroTemp)
            .then(response => {
                jQuery('#dialogUpdateOuDir').modal('hide');
                this.ouCadastroTemp = "";
                this.editavel = true;
            })
            .catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
                this.editavel = true;
            });
    }

	ngOnInit() {
		this.editavel = false;
		this.unidadeOrganizacionalService.getOuDirRoot() 
			.then(response => {
				this.unidadeOrganizacionalService.listar() 
					.then(response => {
						this.status = this.COMPLETE;
						this.editavel = true;
					})
					.catch(response => {
						this.erroAviso = true;
						this.aviso = this.erroHttp(response);
						this.status = this.ERROR;
						alert(this.aviso);
					})
			})
			.catch(response => {
				this.erroAviso = true;
				this.aviso = this.erroHttp(response);
				this.status = this.ERROR;
				alert(this.aviso);
			})
	}

}

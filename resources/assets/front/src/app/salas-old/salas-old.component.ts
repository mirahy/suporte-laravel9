import { Component, OnInit } from '@angular/core';
import { SalasOldService } from '../salas-old.service';
import { SalaOld } from './sala-old';
import { AbstractComponent } from '../abstract-component';
import { DadosService } from '../dados.service';
import { Status } from '../status';
declare var jQuery: any;

@Component({
    selector: 'app-salas-old',
    templateUrl: './salas-old.component.html',
    styleUrls: ['./salas-old.component.less']
  })
export class SalasOldComponent extends AbstractComponent implements OnInit {

    readonly STATUS_INICIAL_PADRAO = Status.CHAVES.PROCESSO;
    readonly STATUS_CONCLUIDO_PADRAO = Status.CHAVES.CONCLUIDO;
    readonly STATUS_REJEITADO_PADRAO = Status.CHAVES.REJEITADO;

    criteria: string = "";

    sala: SalaOld = new SalaOld(0, '', '', '', '','', '', '', '', '', '', '', null, new Date());

    statusTemp:string = "";
    tituloMensagem:string = "";
    mensagem:string = "";
    

    constructor(private salasService: SalasOldService, private dadosService:DadosService) {
        super();
    }

    get salas(): Array<SalaOld> {
        return this.salasService.salas;
    }

    set salas(salas: Array<SalaOld>) {
        this.salasService.salas = salas;
    }
    get modalidades() {
        return this.salasService.modalidades;
    }
    get objetivosSalas() {
        return this.salasService.objetivosSalas;
    }

    editarVisualizar(sala) {
        this.sala = sala.clone();
        console.log(this.sala);
        this.aviso = "";
        if (sala.status.chave == this.STATUS_INICIAL_PADRAO) {
            this.editavel = true;
            //window.location.href = '/salas/' + sala.id + '/edit';
        }
        else
            this.editavel = false;
        //window.location.href = ('/salas/' + sala.id + '/' + (sala.status.chave == STATUS_INICIAL_PADRAO ? 'edit' : ''));
    }

    preparaSetStatusSala(sala:SalaOld, isConcluido:boolean) {
        this.sala = sala;
        this.editavel = true;
        this.mensagem = "";
        if (isConcluido) {
            this.statusTemp = this.STATUS_CONCLUIDO_PADRAO;
            this.tituloMensagem = "Informe o link da sala criada:"
        }
        else {
            this.statusTemp = this.STATUS_REJEITADO_PADRAO;
            this.tituloMensagem = "Informe uma justificativa para a rejeição:"
        }
    }

    statusSala() {
        this.editavel = false;
        this.salasService.statusSala (this.sala,this.statusTemp,this.mensagem)
            .then(response => {
                if (response) {
                    alert (this.erroHttp(response))
                    return;
                }
                jQuery('#dialogStatus').modal('hide');
            })
            .catch(response => {
                alert (this.erroHttp(response));
                jQuery('#dialogStatus').modal('hide');
            });
    }

    atualizarSala() {
        this.salasService.atualizarSala(this.sala)
            .then(response => {
                if (response) {
                    this.erroAviso = true;
                    this.aviso = this.erroHttp(response);
                }
                else {
                    this.erroAviso = false;
                    this.aviso = "Sala Atualizada!";
                    jQuery('#dialogSalas').modal('hide');
                }
            }).catch(response => {
                this.erroAviso = true;
                this.aviso = this.erroHttp(response);
            })

    }

    ngOnInit() {
        this.dadosService.statusList()
            .then(response => {
                this.salasService.getObjetivosSalas()
                    .then(response => {
                        this.salasService.getModalidades()
                            .then(response => {
                                this.salasService.listar()
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
            })
            .catch(response => {
                this.status = this.ERROR;
                console.log(response)
            })
    }

}

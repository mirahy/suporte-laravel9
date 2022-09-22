import { Component, OnInit } from '@angular/core';
import { UsuarioService } from '../usuario.service';
import { AbstractComponent } from '../abstract-component';
import { Usuario } from '../usuarios/usuario';
import { RecursoService } from '../recurso.service';


@Component({
  selector: 'app-select-usuario',
  templateUrl: './select-usuario.component.html',
  styleUrls: ['./select-usuario.component.less']
})
export class SelectUsuarioComponent extends AbstractComponent implements OnInit {

    criteria:string="";

    usuarioTemp:Usuario;

    constructor(private usuarioService: UsuarioService, private recursoService:RecursoService) {
        super();
    }

    get usuarios(): Array<Usuario> {
        return this.usuarioService.usuarios;
    }

    set usuarios(usuarios: Array<Usuario>) {
        this.usuarioService.usuarios = usuarios;
    }

    resetEdit() {
        this.usuarioTemp = new Usuario(0,"","","");
    }

    addGestor(usuario) {
        if (confirm("Confirmar Adição deste Gestor?"))
        this.recursoService.adicionaGestorRecurso(usuario)
            .then(response => {

            })
            .catch (response => {

            })
    }

    ngOnInit() {
        this.resetEdit();

        this.usuarioService.listaUsuarios()
            .then(response => {
                this.status = this.COMPLETE;
            })
            .catch(response => {
                this.status = this.ERROR;
                console.log(response)
            })
    }

}

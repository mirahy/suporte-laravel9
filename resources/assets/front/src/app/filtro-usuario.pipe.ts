import { Pipe, PipeTransform } from '@angular/core';
import { Usuario } from './usuarios/usuario';

@Pipe({
  name: 'filtroUsuario'
})
export class FiltroUsuarioPipe implements PipeTransform {

  transform(value: Array<Usuario>, criteria: string): Array<Usuario> {
    if(criteria == "")
      return value;

    var usuarios:Array<Usuario> = [];

    for(var i in value){
      if(this.filtro(value[i], criteria.toUpperCase()))
        usuarios.push(value[i])
    }
    return usuarios;
  }

  filtro(usuario:Usuario, criteria:string):boolean{
    if(usuario.name.toUpperCase().search(criteria) >= 0 ||
      usuario.email.toUpperCase().search(criteria) >= 0 ||
      usuario.permissao.startsWith(criteria))
      return true;


    return false;
  }

}

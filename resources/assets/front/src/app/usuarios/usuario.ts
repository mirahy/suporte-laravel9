export class Usuario {
    
	id:number;
    name:string;
    email:string;
    permissao:string;
    gestor:boolean;

    constructor (id:number|object,name?:string,email?:string,permissao?:string,gestor?:boolean) {
        if (typeof id == 'number') {
            this.id = id;
            this.name = name;
            this.email = email;
            this.permissao = permissao;
            this.gestor = gestor;
        }
        else {
            this.id = parseInt(id['id']);
            this.name = id['name'];
            this.email = id['email'];
            this.permissao = id['permissao'];
            this.gestor = id['gestor'];
        }
    }

    public static generateList (list:Array<any>):Array<Usuario> {
        var usuarioList: Array<Usuario> = [];
        list.forEach(usuarioAny => {
            var usuario = new Usuario(usuarioAny)
            usuarioList.push(usuario);
        });
        return usuarioList;
    };

    public clone ():Usuario {
        return new Usuario (this);
    }
}
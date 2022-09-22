export class Arvore {

    id:number;
    dado:any;
    pai: Arvore = null;
    filhos = {}; // lista de árvores

    constructor (id:number, dado:any) {
        this.id = id;
        this.dado = dado;
    }

    // Encontra o nó correspondente ao id informado
    find (id:number|null):Arvore {
        if (this.id == id || id == null)
            return this;
        for (var i in this.filhos) {
            var buscado = this.filhos[i].find(id);
            if(buscado)
                return buscado;
        }
        return null;
    }

    // insere um nó na árvore, como filho do nó-id informado
    insert (filho:Arvore, paiId?:number) {
        if (!(filho instanceof Arvore))
            return false;
        
        var pai:Arvore = this.find(paiId);
        if (pai) {
            pai.filhos[filho.id] = filho;
            filho.pai = pai;
        }
        else {
            this.filhos[filho.id] = filho;
            filho.pai = this;
        } 
        return true;
    }

    // remove um nó da árvore por id, se upper for true, transfere os filhos desse nó para seu pai, caso seja false, exclui recursivamente
    delete (id?:number, upper:boolean = false) {
        var nodo:Arvore = this.find(id);
        if (nodo) {
            if (nodo.isRaiz())
                return null;
            if (upper) {
                nodo.upper();
            }
            else {
                for (var i in nodo.filhos)
                    nodo.filhos[i].delete();
            }
            delete(nodo.pai.filhos[id]);
        }
        return nodo;
    }

    // Transfere todos os filhos deste nodo para seu pai
    upper () {
        if (this.pai){
            for (var i in this.filhos) {
                this.filhos[i].pai = this.pai;
                this.pai.filhos[i] = this.filhos[i];
            }
            this.filhos = [];
        }
        else
            return false;
        return true;
    }

    // altera um nodo de lugar (troca de pai)
    change (id:number, novoPaiId:number) {
        //var nodo = this.delete(id);
        var nodo:Arvore = this.find(id);
        
        if (nodo && !nodo.isRaiz() && this.insert(nodo, novoPaiId)) 
            return nodo;
        return null;
    }
    
    isAncestral (id, descendenteId) {
        var nodo:Arvore = this.find(id);
        if (nodo.find(descendenteId)) 
            return true;
        else 
            return false;
    }

    isRaiz () {
        return this.pai == null;
    }

    isFolha () {
        return Object.keys (this.filhos).length == 0;
    }

    // função de sort, apenas esqueleto, para ser sobrescrita por função apropriada, dependendo da aplicação
    sortFilhosArray (a1,a2) {
        return 0;
    }

    get filhosArray () : Arvore[] {
        var filhos = [];
        for(var i in this.filhos) 
            filhos.push (this.filhos[i]);
        this.sortFilhosArray ? filhos.sort(this.sortFilhosArray) : filhos;
        return filhos;
    }
}
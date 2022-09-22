export class Arquivo {
    name: string;
    path: string;
    created: Date;

    constructor (name:(string|any),path?:string, created?:Date) {
        if (typeof name == 'string') {
            this.name = name;
            this.path = path.replace(/\\/g, '/');
            this.created = new Date (created);
        }
        else {
            this.name = name.name;
            this.path = name.path.replace(/\\/g, '/');
            this.created = new Date (name.created);
        }
    }
}
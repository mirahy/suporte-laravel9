import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'formatadorData'
})
export class FormatadorDataPipe implements PipeTransform {

    transform(value: string, time?:boolean): string {
        var date:Date = new Date (value);
        return date.toLocaleDateString() + (time ? " " + date.toLocaleTimeString("pt-BR", {timeZone: "UTC"}).substring(0,5) : '');
    }

}

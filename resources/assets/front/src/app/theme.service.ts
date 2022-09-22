import { Component, OnInit, Injectable } from '@angular/core';
import { Http, Response, Headers } from '@angular/http';
import { Router } from '@angular/router';

import { Theme } from './agenda/theme';
import { Subject, Observable } from 'rxjs';


@Injectable()
export class ThemeService {
    themes: Array<Theme>;
    selectedTheme: string;
    private theme$: Subject<Theme>;

    constructor(private router: Router) {
        this.theme$ = <Subject<Theme>>new Subject();
        this.themes = Theme.getThemes();
    }

    getThemes() {
        return Theme.getThemes();
    }

    setTheme(theme: string) {
        if (theme === undefined || theme === null || theme == "") {
            theme = (theme === null || theme === undefined || theme === "" ? "luna-green" : theme);
        }

        this.selectedTheme = theme;
        var ft = this.findTheme(this.selectedTheme);
        var d = document.getElementById('themeStyleSheet')
        d.setAttribute('href', this.getThemePath(theme));
        this.setNewTheme(ft);
    }

    findTheme(themeName: string): Theme {
        var theme: Theme;
        for (let t of this.getThemes()) {
            if (t.name === themeName) {
                theme = t;
                break;
            }
        }

        return theme;
    }

    getThemePath(theme: string): string {
        var path = "";
        for (let t of this.getThemes()) {
            if (t.name === theme) {
                path = t.path;
                break;
            }
        }

        return path;
    }

    getAppPageHeaderDivStyle(): Object {
        var style = { "border-width": "1px", "border-style": "solid", "border-color": "", "background-color": "", "padding": "" };
        this.selectedTheme = (this.selectedTheme === null || this.selectedTheme === undefined || this.selectedTheme === "" ? "Black-Tie" : this.selectedTheme);
        var theme = this.findTheme(this.selectedTheme);
        style["border-color"] = theme.contentBorderColor;
        style["background-color"] = theme.contentBackgroundColor;
        style["padding"] = "2px";
        return style;
    }

    setNewTheme(theme: Theme): void {
        this.theme$.next(theme);
    }

    getNewTheme(): Observable<Theme> {
        return this.theme$.asObservable();
    }

}
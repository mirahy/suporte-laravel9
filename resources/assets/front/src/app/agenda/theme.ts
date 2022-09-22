export class Theme {
    name: string;
    path: string;
    contentBorderColor: string;
    contentBackgroundColor: string;
    themeType: string;

    constructor(name: string, path: string) {
        this.name = name;
        this.path = path;
    }

    public static getThemes(): Theme[] {
        var themeArray: Theme[] = [];
        var basePath: string = "css/themes/";
        var endPath: string = "/theme.css";

        themeArray.push(new Theme("bootstrap", basePath + "bootstrap" + endPath));
        themeArray.push(new Theme("cruze", basePath + "cruze" + endPath));
        themeArray.push(new Theme("cupertino", basePath + "cupertino" + endPath));
        themeArray.push(new Theme("darkness", basePath + "darkness" + endPath));
        themeArray.push(new Theme("flick", basePath + "flick" + endPath));
        themeArray.push(new Theme("home", basePath + "home" + endPath));
        themeArray.push(new Theme("kasper", basePath + "kasper" + endPath));
        themeArray.push(new Theme("lightness", basePath + "lightness" + endPath));
        themeArray.push(new Theme("ludvig", basePath + "ludvig" + endPath));
        themeArray.push(new Theme("luna-amber", basePath + "luna-amber" + endPath));
        themeArray.push(new Theme("luna-blue", basePath + "luna-blue" + endPath));
        themeArray.push(new Theme("luna-green", basePath + "luna-green" + endPath));
        themeArray.push(new Theme("luna-pink", basePath + "luna-pink" + endPath));
        themeArray.push(new Theme("nova-colored", basePath + "nova-colored" + endPath));
        themeArray.push(new Theme("nova-dark", basePath + "nova-dark" + endPath));
        themeArray.push(new Theme("nova-light", basePath + "nova-light" + endPath));
        themeArray.push(new Theme("omega", basePath + "omega" + endPath));
        themeArray.push(new Theme("pepper-grinder", basePath + "pepper-grinder" + endPath));
        themeArray.push(new Theme("redmond", basePath + "redmond" + endPath));
        themeArray.push(new Theme("rhea", basePath + "rhea" + endPath));
        themeArray.push(new Theme("rocket", basePath + "rocket" + endPath));
        themeArray.push(new Theme("south-street", basePath + "south-street" + endPath));
        themeArray.push(new Theme("start", basePath + "start" + endPath));
        themeArray.push(new Theme("trontastic", basePath + "trontastic" + endPath));
        themeArray.push(new Theme("voclain", basePath + "voclain" + endPath));

        return themeArray;
    }
}
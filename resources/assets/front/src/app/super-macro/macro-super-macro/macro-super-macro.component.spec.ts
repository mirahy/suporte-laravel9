import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MacroSuperMacroComponent } from './macro-super-macro.component';

describe('MacroSuperMacroComponent', () => {
  let component: MacroSuperMacroComponent;
  let fixture: ComponentFixture<MacroSuperMacroComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MacroSuperMacroComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MacroSuperMacroComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

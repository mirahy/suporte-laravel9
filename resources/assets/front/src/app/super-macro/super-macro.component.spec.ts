import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SuperMacroComponent } from './super-macro.component';

describe('SuperMacroComponent', () => {
  let component: SuperMacroComponent;
  let fixture: ComponentFixture<SuperMacroComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SuperMacroComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SuperMacroComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

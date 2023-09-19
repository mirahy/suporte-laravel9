import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MeusCursosComponent } from './meus-cursos.component';

describe('MeusCursosComponent', () => {
  let component: MeusCursosComponent;
  let fixture: ComponentFixture<MeusCursosComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MeusCursosComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MeusCursosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

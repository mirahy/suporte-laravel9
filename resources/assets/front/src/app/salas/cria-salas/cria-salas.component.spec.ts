import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CriaSalasComponent } from './cria-salas.component';

describe('CriaSalasComponent', () => {
  let component: CriaSalasComponent;
  let fixture: ComponentFixture<CriaSalasComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CriaSalasComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CriaSalasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

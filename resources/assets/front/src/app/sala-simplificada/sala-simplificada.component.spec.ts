import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SalaSimplificadaComponent } from './sala-simplificada.component';

describe('SalaSimplificadaComponent', () => {
  let component: SalaSimplificadaComponent;
  let fixture: ComponentFixture<SalaSimplificadaComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SalaSimplificadaComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SalaSimplificadaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

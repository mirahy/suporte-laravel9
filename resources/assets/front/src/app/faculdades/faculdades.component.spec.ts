import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FaculdadesComponent } from './faculdades.component';

describe('FaculdadesComponent', () => {
  let component: FaculdadesComponent;
  let fixture: ComponentFixture<FaculdadesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FaculdadesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FaculdadesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

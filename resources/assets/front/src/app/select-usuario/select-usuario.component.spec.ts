import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SelectUsuarioComponent } from './select-usuario.component';

describe('SelectUsuarioComponent', () => {
  let component: SelectUsuarioComponent;
  let fixture: ComponentFixture<SelectUsuarioComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SelectUsuarioComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SelectUsuarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

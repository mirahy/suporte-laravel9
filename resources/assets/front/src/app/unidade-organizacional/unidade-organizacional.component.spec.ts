import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UnidadeOrganizacionalComponent } from './unidade-organizacional.component';

describe('UnidadeOrganizacionalComponent', () => {
  let component: UnidadeOrganizacionalComponent;
  let fixture: ComponentFixture<UnidadeOrganizacionalComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ UnidadeOrganizacionalComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UnidadeOrganizacionalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

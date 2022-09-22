import { TestBed } from '@angular/core/testing';

import { PlDisciplinasAcademicosService } from './pl-disciplinas-academicos.service';

describe('PlDisciplinasAcademicosService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: PlDisciplinasAcademicosService = TestBed.get(PlDisciplinasAcademicosService);
    expect(service).toBeTruthy();
  });
});

import { TestBed } from '@angular/core/testing';

import { PeriodoLetivosCategoriasService } from './periodo-letivos-categorias.service';

describe('PeriodoLetivosCategoriasService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: PeriodoLetivosCategoriasService = TestBed.get(PeriodoLetivosCategoriasService);
    expect(service).toBeTruthy();
  });
});

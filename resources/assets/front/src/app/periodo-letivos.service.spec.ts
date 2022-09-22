import { TestBed } from '@angular/core/testing';

import { PeriodoLetivosService } from './periodo-letivos.service';

describe('PeriodoLetivosService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: PeriodoLetivosService = TestBed.get(PeriodoLetivosService);
    expect(service).toBeTruthy();
  });
});

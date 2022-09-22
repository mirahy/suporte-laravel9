import { TestBed } from '@angular/core/testing';

import { SalaSimplificadaService } from './sala-simplificada.service';

describe('SalaSimplificadaService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: SalaSimplificadaService = TestBed.get(SalaSimplificadaService);
    expect(service).toBeTruthy();
  });
});
